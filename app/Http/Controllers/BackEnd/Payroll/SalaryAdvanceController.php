<?php

namespace App\Http\Controllers\BackEnd\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\SalaryAdvance;
use App\Services\StaffUserResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryAdvanceController extends Controller
{
    public function __construct(private StaffUserResolver $staffUserResolver) {}

    public function index(Request $request): View
    {
        $query = SalaryAdvance::query()->with(['staff', 'approver'])->latest('date');

        if ($request->filled('staff_key')) {
            $staff = $this->staffUserResolver->resolveByStaffKey((string) $request->input('staff_key'));

            if ($staff) {
                $query->where('staff_type', $staff->getMorphClass())->where('staff_id', (int) $staff->getAuthIdentifier());
            }
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', (int) $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('date', (int) $request->year);
        }

        $advances = $query->paginate(25)->withQueryString();
        $users = $this->staffUserResolver->allActiveStaff();

        return view('backEnd.admin.payroll.advances', compact('advances', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_key' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $staff = $this->resolveStaffByKeyOrFail($validated['staff_key']);
        $approvedBy = $this->staffUserResolver->resolveAuthenticatedStaffUser();

        SalaryAdvance::query()->create([
            'staff_type' => $staff->getMorphClass(),
            'staff_id' => (int) $staff->getAuthIdentifier(),
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'note' => $validated['note'] ?? null,
            'approved_by_type' => $approvedBy ? $approvedBy->getMorphClass() : null,
            'approved_by_id' => $approvedBy ? (int) $approvedBy->getAuthIdentifier() : null,
        ]);

        return back()->with('success', 'Salary advance created.');
    }

    public function update(Request $request, SalaryAdvance $salaryAdvance): RedirectResponse
    {
        $validated = $request->validate([
            'staff_key' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $staff = $this->resolveStaffByKeyOrFail($validated['staff_key']);

        $salaryAdvance->update([
            'staff_type' => $staff->getMorphClass(),
            'staff_id' => (int) $staff->getAuthIdentifier(),
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'note' => $validated['note'] ?? null,
        ]);

        return back()->with('success', 'Salary advance updated.');
    }

    public function destroy(SalaryAdvance $salaryAdvance): RedirectResponse
    {
        $salaryAdvance->delete();

        return back()->with('success', 'Salary advance deleted.');
    }

    private function resolveStaffByKeyOrFail(string $staffKey): Admin|Manager|Employee
    {
        $staff = $this->staffUserResolver->resolveByStaffKey($staffKey);
        abort_unless($staff, 422, 'Invalid staff selected.');

        return $staff;
    }
}
