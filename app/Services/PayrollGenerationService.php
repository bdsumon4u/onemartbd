<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Attendance;
use App\Models\MonthlyPayroll;
use App\Models\PayrollSetting;
use App\Models\SalaryAdvance;
use App\Models\User;
use App\Models\UserBonus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PayrollGenerationService
{
    public function __construct(private AttendanceCalculationService $attendanceCalculationService) {}

    public function generateForUser(User $user, int $month, int $year, ?int $generatedBy = null): MonthlyPayroll
    {
        $settings = PayrollSetting::current();
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();

        $totalDays = $monthStart->daysInMonth;
        $workingDays = $this->calculateWorkingDays($user, $monthStart, $monthEnd);

        $attendances = Attendance::query()
            ->where('user_id', $user->id)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get();

        $attendances = $this->normalizeIncompleteAttendances($attendances, $user, $settings);

        $presentDays = $attendances->where('status', 'present')->count();
        $offDayPresents = $attendances->where('status', 'present')->where('is_off_day', true)->count();
        $regularPresent = max(0, $presentDays - $offDayPresents);
        $absentDays = max(0, $workingDays - $regularPresent);

        $monthlySalary = (float) $user->monthly_salary;
        $dailySalary = $totalDays > 0 ? $monthlySalary / $totalDays : 0;
        $baseSalary = $dailySalary * $regularPresent;
        $offDayBonus = $dailySalary * 1.5 * $offDayPresents;

        [$overtimeAmount, $lateDeduction, $totalLateMinutes] = $this->calculateOvertimeAndLateDeduction(
            attendances: $attendances,
            user: $user,
            settings: $settings,
            dailySalary: $dailySalary,
        );

        $penaltyAmount = (float) $attendances->sum('penalty_amount');

        $haziraBonusAmount = $absentDays === 0 && $totalLateMinutes === 0
            ? (float) $settings->hazira_bonus
            : 0;

        $monthString = str_pad((string) $month, 2, '0', STR_PAD_LEFT);

        $occasionalBonusAmount = (float) UserBonus::query()
            ->where('user_id', $user->id)
            ->where('year', $year)
            ->where('month', $monthString)
            ->sum('amount');

        $xsellBonusAmount = $this->calculateXsellBonus($user, $settings, $month, $year);

        $advanceDeduction = (float) SalaryAdvance::query()
            ->where('user_id', $user->id)
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
                'user_id' => $user->id,
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
                'generated_by' => $generatedBy,
                'status' => 'draft',
            ]
        );
    }

    public function generateForAll(int $month, int $year, ?int $generatedBy = null): void
    {
        User::query()
            ->whereIn('role', [1, 2, 3])
            ->where('status', 1)
            ->each(function (User $user) use ($month, $year, $generatedBy): void {
                $this->generateForUser($user, $month, $year, $generatedBy);
            });
    }

    private function calculateWorkingDays(User $user, Carbon $monthStart, Carbon $monthEnd): int
    {
        $workingDays = 0;
        $date = $monthStart->copy();

        while ($date->lte($monthEnd)) {
            if (! $user->isOffDay($date)) {
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
    private function normalizeIncompleteAttendances(Collection $attendances, User $user, PayrollSetting $settings): Collection
    {
        return $attendances->map(function (Attendance $attendance) use ($user, $settings): Attendance {
            if ($attendance->status !== 'present' || ! $attendance->check_in || $attendance->check_out) {
                return $attendance;
            }

            $checkIn = Carbon::parse($attendance->check_in);
            [, $scheduledEnd] = $this->attendanceCalculationService->scheduledDateTimes($user, Carbon::parse($attendance->date));

            if ($attendance->date->isToday() && now()->lt($scheduledEnd)) {
                $virtualCheckout = $scheduledEnd->copy();
                $snapshot = clone $attendance;
                $snapshot->check_out = $virtualCheckout;
                $this->attendanceCalculationService->applyOffsetToAttendance($snapshot, $user, $checkIn, $virtualCheckout);

                return $snapshot;
            }

            $attendance->check_out = $scheduledEnd;
            $attendance->auto_checkout = true;
            $attendance->penalty_amount = $settings->forgot_checkout_penalty;
            $this->attendanceCalculationService->applyOffsetToAttendance($attendance, $user, $checkIn, $scheduledEnd);
            $attendance->save();

            return $attendance;
        });
    }

    /**
     * @param  Collection<int,Attendance>  $attendances
     * @return array{0:float,1:float,2:int}
     */
    private function calculateOvertimeAndLateDeduction(Collection $attendances, User $user, PayrollSetting $settings, float $dailySalary): array
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
                $calculated = $this->attendanceCalculationService->calculateOffset($user, $checkIn, $checkOut);
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

    private function calculateXsellBonus(User $user, PayrollSetting $settings, int $month, int $year): float
    {
        if ((int) $user->role !== 3) {
            return 0.0;
        }

        $qualifyingCount = \App\Models\Order::query()
            ->join('order_assigns', 'order_assigns.order_id', '=', 'orders.id')
            ->join('employees', 'employees.id', '=', 'order_assigns.employee_id')
            ->where('employees.email', $user->email)
            ->where('orders.status', OrderStatus::Delivered->value)
            ->whereNotNull('orders.delivered_at')
            ->whereMonth('orders.delivered_at', $month)
            ->whereYear('orders.delivered_at', $year)
            ->whereColumn('orders.delivered_quantity', '>', 'orders.ordered_quantity')
            ->count();

        return $qualifyingCount * (float) $settings->xsell_bonus_rate;
    }
}
