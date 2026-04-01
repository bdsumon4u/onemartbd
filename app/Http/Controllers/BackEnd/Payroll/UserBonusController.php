<?php

namespace App\Http\Controllers\BackEnd\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\UserBonus;
use App\Services\StaffUserResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserBonusController extends Controller
{
    public function __construct(private StaffUserResolver $staffUserResolver) {}

    public function index(Request $request): View
    {
        $query = UserBonus::query()->with('staff')->latest('id');

        if ($request->filled('staff_key')) {
            $staff = $this->staffUserResolver->resolveByStaffKey((string) $request->input('staff_key'));

            if ($staff) {
                $query->where('staff_type', $staff->getMorphClass())->where('staff_id', (int) $staff->getAuthIdentifier());
            }
        }

        if ($request->filled('month')) {
            $query->where('month', str_pad((string) $request->month, 2, '0', STR_PAD_LEFT));
        }

        if ($request->filled('year')) {
            $query->where('year', (int) $request->year);
        }

        $bonuses = $query->paginate(25)->withQueryString();
        $users = $this->staffUserResolver->allActiveStaff();

        return view('backEnd.admin.payroll.bonuses', compact('bonuses', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'staff_key' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'year' => ['required', 'integer', 'between:2020,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'notes' => ['nullable', 'string'],
        ]);

        $staff = $this->resolveStaffByKeyOrFail($validated['staff_key']);

        UserBonus::query()->create([
            'staff_type' => $staff->getMorphClass(),
            'staff_id' => (int) $staff->getAuthIdentifier(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'year' => $validated['year'],
            'notes' => $validated['notes'] ?? null,
            'month' => str_pad((string) $validated['month'], 2, '0', STR_PAD_LEFT),
        ]);

        return back()->with('success', 'User bonus created.');
    }

    public function update(Request $request, UserBonus $userBonus): RedirectResponse
    {
        $validated = $request->validate([
            'staff_key' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'year' => ['required', 'integer', 'between:2020,2100'],
            'month' => ['required', 'integer', 'between:1,12'],
            'notes' => ['nullable', 'string'],
        ]);

        $staff = $this->resolveStaffByKeyOrFail($validated['staff_key']);

        $userBonus->update([
            'staff_type' => $staff->getMorphClass(),
            'staff_id' => (int) $staff->getAuthIdentifier(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
            'year' => $validated['year'],
            'notes' => $validated['notes'] ?? null,
            'month' => str_pad((string) $validated['month'], 2, '0', STR_PAD_LEFT),
        ]);

        return back()->with('success', 'User bonus updated.');
    }

    public function destroy(UserBonus $userBonus): RedirectResponse
    {
        $userBonus->delete();

        return back()->with('success', 'User bonus deleted.');
    }

    private function resolveStaffByKeyOrFail(string $staffKey): Admin|Manager|Employee
    {
        $staff = $this->staffUserResolver->resolveByStaffKey($staffKey);
        abort_unless($staff, 422, 'Invalid staff selected.');

        return $staff;
    }
}
