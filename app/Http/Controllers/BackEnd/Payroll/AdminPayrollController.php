<?php

namespace App\Http\Controllers\BackEnd\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\MonthlyPayroll;
use App\Models\PayrollSetting;
use App\Models\SalaryAdvance;
use App\Models\User;
use App\Models\UserBonus;
use App\Services\PayrollGenerationService;
use App\Services\StaffUserResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            $this->payrollGenerationService->generateForAll($month, $year, $actor?->id);
        }

        $payrolls = MonthlyPayroll::query()
            ->with('user')
            ->where('month', $month)
            ->where('year', $year)
            ->orderByDesc('net_salary')
            ->paginate(30)
            ->withQueryString();

        $staffUsers = User::query()->whereIn('role', [1, 2, 3])->where('status', 1)->orderBy('name')->get();

        return view('backEnd.admin.payroll.index', compact('payrolls', 'month', 'year', 'staffUsers'));
    }

    public function generateAll(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2020,2100'],
        ]);

        $actor = $this->staffUserResolver->resolveAuthenticatedStaffUser();
        $this->payrollGenerationService->generateForAll((int) $validated['month'], (int) $validated['year'], $actor?->id);

        return back()->with('success', 'Payroll generated for all staff.');
    }

    public function generateSingle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'month' => ['required', 'integer', 'between:1,12'],
            'year' => ['required', 'integer', 'between:2020,2100'],
        ]);

        $actor = $this->staffUserResolver->resolveAuthenticatedStaffUser();
        $user = User::query()->findOrFail((int) $validated['user_id']);
        $this->payrollGenerationService->generateForUser($user, (int) $validated['month'], (int) $validated['year'], $actor?->id);

        return back()->with('success', 'Payroll generated for selected user.');
    }

    public function show(MonthlyPayroll $payroll): View
    {
        $attendances = Attendance::query()
            ->where('user_id', $payroll->user_id)
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->orderBy('date')
            ->get();

        $advances = SalaryAdvance::query()
            ->where('user_id', $payroll->user_id)
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->get();

        $bonuses = UserBonus::query()
            ->where('user_id', $payroll->user_id)
            ->where('month', $payroll->month)
            ->where('year', $payroll->year)
            ->get();

        $settings = PayrollSetting::current();

        return view('backEnd.admin.payroll.show', compact('payroll', 'attendances', 'advances', 'bonuses', 'settings'));
    }

    public function print(MonthlyPayroll $payroll): View
    {
        $attendances = Attendance::query()
            ->where('user_id', $payroll->user_id)
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->orderBy('date')
            ->get();

        $advances = SalaryAdvance::query()
            ->where('user_id', $payroll->user_id)
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->get();

        $bonuses = UserBonus::query()
            ->where('user_id', $payroll->user_id)
            ->where('month', $payroll->month)
            ->where('year', $payroll->year)
            ->get();

        $settings = PayrollSetting::current();

        return view('backEnd.admin.payroll.print', compact('payroll', 'attendances', 'advances', 'bonuses', 'settings'));
    }

    public function updateStatus(Request $request, MonthlyPayroll $payroll): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,approved,paid'],
        ]);

        $payroll->update(['status' => $validated['status']]);

        return back()->with('success', 'Payroll status updated successfully.');
    }
}
