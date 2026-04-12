<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Manager;
use App\Models\MonthlyPayroll;
use App\Models\PayrollSetting;
use App\Models\SalaryAdvance;
use App\Models\UserBonus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PayrollGenerationService
{
    public function __construct(private AttendanceCalculationService $attendanceCalculationService) {}

    public function generateForUser(Admin|Manager|Employee $staff, int $month, int $year, Admin|Manager|Employee|null $generatedBy = null): MonthlyPayroll
    {
        $settings = PayrollSetting::current();
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();
        $holidayDates = $this->resolveHolidayDateSet($monthStart, $monthEnd);
        $staffType = $staff->getMorphClass();
        $staffId = (int) $staff->getAuthIdentifier();

        $totalDays = $monthStart->daysInMonth;
        $workingDays = $this->calculateWorkingDays($staff, $monthStart, $monthEnd, $holidayDates);

        $attendances = Attendance::query()
            ->where('staff_type', $staffType)
            ->where('staff_id', $staffId)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        $attendances = $this->normalizeIncompleteAttendances($attendances, $staff, $settings);

        $presentAttendances = $attendances
            ->where('status', 'present')
            ->unique(fn (Attendance $attendance): string => Carbon::parse($attendance->date)->toDateString())
            ->values();

        $presentDateSet = $presentAttendances
            ->map(fn (Attendance $attendance): string => Carbon::parse($attendance->date)->toDateString())
            ->unique()
            ->values();

        $holidayAbsentCount = $holidayDates->diff($presentDateSet)->count();

        $actualRegularPresent = $presentAttendances
            ->filter(function (Attendance $attendance) use ($holidayDates): bool {
                $dateString = Carbon::parse($attendance->date)->toDateString();

                return ! $attendance->is_off_day && ! $holidayDates->contains($dateString);
            })
            ->count();

        $offDayPresents = $presentAttendances
            ->filter(function (Attendance $attendance) use ($holidayDates): bool {
                $dateString = Carbon::parse($attendance->date)->toDateString();

                return $attendance->is_off_day || $holidayDates->contains($dateString);
            })
            ->count();

        $presentDays = $presentDateSet->count() + $holidayAbsentCount;
        $regularPresent = $actualRegularPresent + $holidayAbsentCount;
        $absentDays = max(0, $workingDays - $actualRegularPresent);

        $monthlySalary = (float) $staff->monthly_salary;
        $dailySalary = $totalDays > 0 ? $monthlySalary / $totalDays : 0;
        $baseSalary = $dailySalary * $regularPresent;
        $offDayBoost = max(1.0, min(3.0, (float) $settings->off_day_salary_boost));
        $offDayBonus = $dailySalary * $offDayBoost * $offDayPresents;

        [$overtimeAmount, $lateDeduction, $totalLateMinutes] = $this->calculateOvertimeAndLateDeduction(
            attendances: $attendances,
            staff: $staff,
            settings: $settings,
            dailySalary: $dailySalary,
        );

        $penaltyAmount = (float) $attendances->sum('penalty_amount');

        $haziraBonusAmount = $absentDays === 0 && $totalLateMinutes === 0
            ? (float) $settings->hazira_bonus
            : 0;

        $monthString = str_pad((string) $month, 2, '0', STR_PAD_LEFT);

        $occasionalBonusAmount = (float) UserBonus::query()
            ->where('staff_type', $staffType)
            ->where('staff_id', $staffId)
            ->where('year', $year)
            ->where('month', $monthString)
            ->sum('amount');

        $xsellBonusAmount = $this->calculateXsellBonus($staff, $settings, $month, $year);

        $advanceDeduction = (float) SalaryAdvance::query()
            ->where('staff_type', $staffType)
            ->where('staff_id', $staffId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');

        $netSalary = $baseSalary
            + $offDayBonus
            + $overtimeAmount
            - $lateDeduction
            - $penaltyAmount
            + $haziraBonusAmount
            + $occasionalBonusAmount
            + $xsellBonusAmount
            - $advanceDeduction;

        return MonthlyPayroll::query()->updateOrCreate(
            [
                'staff_type' => $staffType,
                'staff_id' => $staffId,
                'month' => $month,
                'year' => $year,
            ],
            [
                'total_days' => $totalDays,
                'working_days' => $workingDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'off_day_presents' => $offDayPresents,
                'daily_salary' => round($dailySalary, 2),
                'base_salary' => round($baseSalary, 2),
                'off_day_bonus' => round($offDayBonus, 2),
                'overtime_amount' => round($overtimeAmount, 2),
                'late_deduction' => round($lateDeduction, 2),
                'penalty_amount' => round($penaltyAmount, 2),
                'hazira_bonus_amount' => round($haziraBonusAmount, 2),
                'occasional_bonus_amount' => round($occasionalBonusAmount, 2),
                'xsell_bonus_amount' => round($xsellBonusAmount, 2),
                'advance_deduction' => round($advanceDeduction, 2),
                'net_salary' => round($netSalary, 2),
                'generated_by_type' => $generatedBy ? $generatedBy->getMorphClass() : null,
                'generated_by_id' => $generatedBy ? (int) $generatedBy->getAuthIdentifier() : null,
                'status' => 'draft',
            ]
        );
    }

    public function generateForAll(int $month, int $year, Admin|Manager|Employee|null $generatedBy = null): void
    {
        Admin::query()
            ->where('status', 1)
            ->each(function (Admin $staff) use ($month, $year, $generatedBy): void {
                $this->generateForUser($staff, $month, $year, $generatedBy);
            });

        Manager::query()
            ->where('status', 1)
            ->each(function (Manager $staff) use ($month, $year, $generatedBy): void {
                $this->generateForUser($staff, $month, $year, $generatedBy);
            });

        Employee::query()
            ->where('status', 1)
            ->each(function (Employee $staff) use ($month, $year, $generatedBy): void {
                $this->generateForUser($staff, $month, $year, $generatedBy);
            });
    }

    private function calculateWorkingDays(Admin|Manager|Employee $staff, Carbon $monthStart, Carbon $monthEnd, Collection $holidayDates): int
    {
        $workingDays = 0;
        $date = $monthStart->copy();

        while ($date->lte($monthEnd)) {
            $dateString = $date->toDateString();

            if ($holidayDates->contains($dateString)) {
                $date->addDay();

                continue;
            }

            if (! $staff->isOffDay($date)) {
                $workingDays++;
            }

            $date->addDay();
        }

        return $workingDays;
    }

    /**
     * @param  Collection<int,Attendance>  $attendances
     * @return Collection<int,Attendance>
     */
    private function normalizeIncompleteAttendances(Collection $attendances, Admin|Manager|Employee $staff, PayrollSetting $settings): Collection
    {
        return $attendances->map(function (Attendance $attendance) use ($staff, $settings): Attendance {
            if ($attendance->status !== 'present' || ! $attendance->check_in || $attendance->check_out) {
                return $attendance;
            }

            $checkIn = Carbon::parse($attendance->check_in);
            [, $scheduledEnd] = $this->attendanceCalculationService->scheduledDateTimes($staff, Carbon::parse($attendance->date));

            if ($attendance->date->isToday() && now()->lt($scheduledEnd)) {
                $virtualCheckout = $scheduledEnd->copy();
                $snapshot = clone $attendance;
                $snapshot->check_out = $virtualCheckout;
                $this->attendanceCalculationService->applyOffsetToAttendance($snapshot, $staff, $checkIn, $virtualCheckout);

                return $snapshot;
            }

            $attendance->check_out = $scheduledEnd;
            $attendance->auto_checkout = true;
            $attendance->penalty_amount = $settings->forgot_checkout_penalty;
            $this->attendanceCalculationService->applyOffsetToAttendance($attendance, $staff, $checkIn, $scheduledEnd);
            $attendance->save();

            return $attendance;
        });
    }

    /**
     * @param  Collection<int,Attendance>  $attendances
     * @return array{0:float,1:float,2:int}
     */
    private function calculateOvertimeAndLateDeduction(Collection $attendances, Admin|Manager|Employee $staff, PayrollSetting $settings, float $dailySalary): array
    {
        $overtimeAmount = 0.0;
        $lateDeduction = 0.0;
        $totalLateMinutes = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->status !== 'present') {
                continue;
            }

            $totalOt = (int) $attendance->overtime_minutes + (int) $attendance->extra_overtime_minutes;
            $otUnits = intdiv($totalOt, max(1, (int) $settings->overtime_unit_minutes));
            $overtimeAmount += $otUnits * (float) $settings->overtime_rate;

            $lateMinutes = (int) $attendance->late_minutes;
            $totalLateMinutes += $lateMinutes;
            $lateUnits = intdiv($lateMinutes, max(1, (int) $settings->latetime_unit_minutes));
            $dailyLateFee = $lateUnits * (float) $settings->latetime_rate;

            $checkIn = $attendance->check_in ? Carbon::parse($attendance->check_in) : null;
            $checkOut = $attendance->check_out ? Carbon::parse($attendance->check_out) : null;

            $workedMinutes = 0;
            $scheduledMinutes = 0;
            if ($checkIn && $checkOut) {
                $calculated = $this->attendanceCalculationService->calculateOffset($staff, $checkIn, $checkOut);
                $workedMinutes = $calculated['worked_minutes'];
                $scheduledMinutes = $calculated['scheduled_minutes'];
            }

            $lateCap = $dailySalary;
            if ($scheduledMinutes > 0 && $workedMinutes >= ($scheduledMinutes / 2)) {
                $lateCap = $dailySalary / 2;
            }

            $lateDeduction += min($dailyLateFee, $lateCap);
        }

        return [round($overtimeAmount, 2), round($lateDeduction, 2), $totalLateMinutes];
    }

    private function calculateXsellBonus(Admin|Manager|Employee $staff, PayrollSetting $settings, int $month, int $year): float
    {
        if (! $staff instanceof Employee) {
            return 0.0;
        }

        $qualifyingCount = \App\Models\Order::query()
            ->join('order_assigns', 'order_assigns.order_id', '=', 'orders.id')
            ->join('employees', 'employees.id', '=', 'order_assigns.employee_id')
            ->where('employees.id', (int) $staff->id)
            ->where('orders.status', OrderStatus::Delivered->value)
            ->whereNotNull('orders.delivered_at')
            ->whereMonth('orders.delivered_at', $month)
            ->whereYear('orders.delivered_at', $year)
            ->whereColumn('orders.delivered_quantity', '>', 'orders.ordered_quantity')
            ->count();

        return $qualifyingCount * (float) $settings->xsell_bonus_rate;
    }

    /**
     * @return Collection<int,string>
     */
    private function resolveHolidayDateSet(Carbon $monthStart, Carbon $monthEnd): Collection
    {
        $holidayDates = collect();

        $holidays = Holiday::query()
            ->overlappingRange($monthStart->toDateString(), $monthEnd->toDateString())
            ->get();

        foreach ($holidays as $holiday) {
            $rangeStart = Carbon::parse($holiday->from_date)->startOfDay();
            $rangeEnd = Carbon::parse($holiday->to_date)->endOfDay();

            if ($rangeStart->lt($monthStart)) {
                $rangeStart = $monthStart->copy();
            }

            if ($rangeEnd->gt($monthEnd)) {
                $rangeEnd = $monthEnd->copy();
            }

            $cursor = $rangeStart->copy();
            while ($cursor->lte($rangeEnd)) {
                $holidayDates->push($cursor->toDateString());
                $cursor->addDay();
            }
        }

        return $holidayDates->unique()->values();
    }
}
