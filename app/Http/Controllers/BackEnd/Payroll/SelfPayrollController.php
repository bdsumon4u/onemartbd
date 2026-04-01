<?php

namespace App\Http\Controllers\BackEnd\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\MonthlyPayroll;
use App\Models\PayrollSetting;
use App\Models\SalaryAdvance;
use App\Models\UserBonus;
use App\Services\StaffUserResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SelfPayrollController extends Controller
{
    public function __construct(private StaffUserResolver $staffUserResolver) {}

    public function index(Request $request): View
    {
        $user = $this->staffUserResolver->resolveAuthenticatedStaffUser();
        abort_unless($user, 403);

        $month = (int) ($request->input('month') ?: now()->month);
        $year = (int) ($request->input('year') ?: now()->year);

        $payrolls = MonthlyPayroll::query()
            ->where('user_id', $user->id)
            ->where('month', $month)
            ->where('year', $year)
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('backEnd.'.$this->panelSlug().'.payroll.my-index', compact('payrolls', 'month', 'year'));
    }

    public function show(MonthlyPayroll $payroll): View
    {
        $user = $this->staffUserResolver->resolveAuthenticatedStaffUser();
        abort_unless($user && $payroll->user_id === $user->id, 403);

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

        return view('backEnd.'.$this->panelSlug().'.payroll.my-show', compact('payroll', 'attendances', 'advances', 'bonuses', 'settings'));
    }

    public function advances(Request $request): View
    {
        $user = $this->staffUserResolver->resolveAuthenticatedStaffUser();
        abort_unless($user, 403);

        $query = SalaryAdvance::query()->where('user_id', $user->id)->latest('date');

        if ($request->filled('month')) {
            $query->whereMonth('date', (int) $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', (int) $request->year);
        }

        $advances = $query->paginate(20)->withQueryString();

        return view('backEnd.'.$this->panelSlug().'.payroll.my-advances', compact('advances'));
    }

    private function panelSlug(): string
    {
        if (auth('admin')->check()) {
            return 'admin';
        }

        if (auth('manager')->check()) {
            return 'manager';
        }

        return 'employee';
    }
}
