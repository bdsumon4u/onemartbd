<?php

namespace App\Http\Controllers\BackEnd\Payroll;

use App\Http\Controllers\Controller;
use App\Models\SalaryAdvance;
use App\Models\User;
use App\Services\StaffUserResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryAdvanceController extends Controller
{
    public function __construct(private StaffUserResolver $staffUserResolver) {}

    public function index(Request $request): View
    {
        $query = SalaryAdvance::query()->with(['user', 'approver'])->latest('date');

        if ($request->filled('user_id')) {
            $query->where('user_id', (int) $request->user_id);
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', (int) $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', (int) $request->year);
        }

        $advances = $query->paginate(25)->withQueryString();
        $users = User::query()->whereIn('role', [1, 2, 3])->where('status', 1)->orderBy('name')->get();

        return view('backEnd.admin.payroll.advances', compact('advances', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $approvedBy = $this->staffUserResolver->resolveAuthenticatedStaffUser()?->id;

        SalaryAdvance::query()->create([
            ...$validated,
            'approved_by' => $approvedBy,
        ]);

        return back()->with('success', 'Salary advance created.');
    }

    public function update(Request $request, SalaryAdvance $salaryAdvance): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $salaryAdvance->update($validated);

        return back()->with('success', 'Salary advance updated.');
    }

    public function destroy(SalaryAdvance $salaryAdvance): RedirectResponse
    {
        $salaryAdvance->delete();

        return back()->with('success', 'Salary advance deleted.');
    }
}
