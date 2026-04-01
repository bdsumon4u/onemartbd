<?php

namespace App\Http\Controllers\BackEnd\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\MonthlyPayroll;
use App\Models\PayrollSetting;
use App\Models\SalaryAdvance;
use App\Models\UserBonus;
use App\Services\PayrollGenerationService;
use App\Services\StaffUserResolver;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AdminPayrollController extends Controller
{
    public function __construct(
        private PayrollGenerationService $payrollGenerationService,
        private StaffUserResolver $staffUserResolver,
    ) {}

    public function index(Request $request): View
    {
        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);

        if ($month === (int) now()->month && $year === (int) now()->year) {
            $actor = $this->staffUserResolver->resolveAuthenticatedStaffUser();
            $this->payrollGenerationService->generateForAll($month, $year, $actor);
        }

        $payrolls = MonthlyPayroll::query()
            ->with('user')
            ->where('month', $month)
            ->where('year', $year)
            ->orderByDesc('net_salary')
            ->paginate(30)
            ->withQueryString();

        $staffUsers = $this->staffUserResolver->allActiveStaff();

        return view('backEnd.admin.payroll.index', compact('payrolls', 'month', 'year', 'staffUsers'));
    }

    public function generateAll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2020,2100'],
        ]);

        $actor = $this->staffUserResolver->resolveAuthenticatedStaffUser();
        $this->payrollGenerationService->generateForAll((int) $validated['month'], (int) $validated['year'], $actor);

        return back()->with('success', 'Payroll generated for all staff.');
    }

    public function generateSingle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_key' => ['required', 'string'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2020,2100'],
        ]);

        $actor = $this->staffUserResolver->resolveAuthenticatedStaffUser();
        $user = $this->resolveStaffByKeyOrFail($validated['staff_key']);
        $this->payrollGenerationService->generateForUser($user, (int) $validated['month'], (int) $validated['year'], $actor);

        return back()->with('success', 'Payroll generated for selected user.');
    }

    public function show(MonthlyPayroll $payroll): View
    {
        $attendances = Attendance::query()
            ->where('staff_type', $payroll->staff_type)
            ->where('staff_id', $payroll->staff_id)
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->orderBy('date')
            ->get();

        $advances = SalaryAdvance::query()
            ->where('staff_type', $payroll->staff_type)
            ->where('staff_id', $payroll->staff_id)
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->get();

        $bonuses = UserBonus::query()
            ->where('staff_type', $payroll->staff_type)
            ->where('staff_id', $payroll->staff_id)
            ->where('month', $payroll->month)
            ->where('year', $payroll->year)
            ->get();

        $settings = PayrollSetting::current();
        [$holidayRanges, $holidayTotalDays] = $this->resolveMonthHolidays($payroll->month, $payroll->year);

        return view('backEnd.admin.payroll.show', compact('payroll', 'attendances', 'advances', 'bonuses', 'settings', 'holidayRanges', 'holidayTotalDays'));
    }

    public function print(MonthlyPayroll $payroll): View
    {
        $attendances = Attendance::query()
            ->where('staff_type', $payroll->staff_type)
            ->where('staff_id', $payroll->staff_id)
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->orderBy('date')
            ->get();

        $advances = SalaryAdvance::query()
            ->where('staff_type', $payroll->staff_type)
            ->where('staff_id', $payroll->staff_id)
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->get();

        $bonuses = UserBonus::query()
            ->where('staff_type', $payroll->staff_type)
            ->where('staff_id', $payroll->staff_id)
            ->where('month', $payroll->month)
            ->where('year', $payroll->year)
            ->get();

        $settings = PayrollSetting::current();
        [$holidayRanges, $holidayTotalDays] = $this->resolveMonthHolidays($payroll->month, $payroll->year);

        return view('backEnd.admin.payroll.print', compact('payroll', 'attendances', 'advances', 'bonuses', 'settings', 'holidayRanges', 'holidayTotalDays'));
    }

    public function updateStatus(Request $request, MonthlyPayroll $payroll): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,approved,paid'],
        ]);

        $payroll->update(['status' => $validated['status']]);

        return back()->with('success', 'Payroll status updated successfully.');
    }

    /**
     * @return array{0:Collection<int,array{name:string,from:Carbon,to:Carbon,days:int}>,1:int}
     */
    private function resolveMonthHolidays(int $month, int $year): array
    {
        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();

        $holidayRanges = Holiday::query()
            ->overlappingRange($monthStart->toDateString(), $monthEnd->toDateString())
            ->orderBy('from_date')
            ->get()
            ->map(function (Holiday $holiday) use ($monthStart, $monthEnd): array {
                $rangeStart = Carbon::parse($holiday->from_date)->startOfDay();
                $rangeEnd = Carbon::parse($holiday->to_date)->endOfDay();

                if ($rangeStart->lt($monthStart)) {
                    $rangeStart = $monthStart->copy();
                }

                if ($rangeEnd->gt($monthEnd)) {
                    $rangeEnd = $monthEnd->copy();
                }

                return [
                    'name' => $holiday->name,
                    'from' => $rangeStart,
                    'to' => $rangeEnd,
                    'days' => $rangeStart->diffInDays($rangeEnd) + 1,
                ];
            });

        $holidayTotalDays = (int) $holidayRanges->sum('days');

        return [$holidayRanges, $holidayTotalDays];
    }

    private function resolveStaffByKeyOrFail(string $staffKey): Authenticatable
    {
        $staff = $this->staffUserResolver->resolveByStaffKey($staffKey);
        abort_unless($staff, 422, 'Invalid staff selected.');

        return $staff;
    }
}
