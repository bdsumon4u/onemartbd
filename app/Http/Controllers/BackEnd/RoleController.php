<?php

namespace App\Http\Controllers\BackEnd;

use App\Enums\RoleType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\OrderAssign;
use App\Models\User;
use App\Services\StaffUserResolver;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RoleController extends Controller
{
    public function __construct(private StaffUserResolver $staffUserResolver) {}

    public function index()
    {
        $data['admin'] = Admin::get();
        $data['manager'] = Manager::get();
        $data['employee'] = Employee::get();

        $this->attachPayrollMeta($data['admin'], RoleType::Admin);
        $this->attachPayrollMeta($data['manager'], RoleType::Manager);
        $this->attachPayrollMeta($data['employee'], RoleType::Employee);

        return view('backEnd.admin.roles.index', compact('data'));
    }

    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();
        $role = RoleType::from((int) $validated['role']);

        $staff = $this->userModel($role)::create(
            $this->buildPayload($validated, $validated['password'])
        );
        $this->syncPayrollUserFromStaff($staff, $role, $validated);

        return redirect()->route($this->roleRoute())->with('success', 'User Created Successfully');
    }

    public function update(UpdateRoleRequest $request)
    {
        $validated = $request->validated();
        $role = RoleType::from((int) $validated['old_role']);

        $user = $this->userModel($role)::query()->findOrFail((int) $validated['id']);
        $status = $this->lockedAdminStatus($validated['id'], $role, (int) $validated['status']);
        $payload = $this->buildPayload($validated, $validated['password'], $user->password, $status);

        $user->update($payload);
        $this->syncPayrollUserFromStaff($user, $role, $validated);

        return redirect()->route($this->roleRoute())->with('success', 'User Updated Successfully');
    }

    public function delete($id, $role)
    {
        $roleType = RoleType::tryFrom((int) $role);
        if (! $roleType) {
            return back()->with('warning', 'Something Went Wrong');
        }

        if ($roleType === RoleType::Employee && OrderAssign::where('employee_id', $id)->exists()) {
            return back()->with('error', 'This User Can\'t Be Deleted');
        }

        $this->userModel($roleType)::query()->findOrFail((int) $id)->delete();

        return back()->with('success', 'Role Deleted Successfully');
    }

    private function buildPayload(array $data, ?string $password = null, ?string $existingPassword = null, ?int $forcedStatus = null): array
    {
        $payload = [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'],
            'status' => $forcedStatus ?? (int) $data['status'],
            'start_time' => $this->parseTime($data['start_time'] ?? null),
            'end_time' => $this->parseTime($data['end_time'] ?? null),
            'password' => $password ? Hash::make($password) : $existingPassword,
        ];

        return $payload;
    }

    private function userModel(RoleType $role): string
    {
        return match ($role) {
            RoleType::Admin => Admin::class,
            RoleType::Manager => Manager::class,
            RoleType::Employee => Employee::class,
        };
    }

    private function parseTime(?string $time): ?string
    {
        return $time ? date('H:i:s', strtotime($time)) : null;
    }

    private function roleRoute(): string
    {
        return Auth::guard('admin')->check() ? 'admin.roles' : 'manager.roles';
    }

    private function lockedAdminStatus(int $id, RoleType $role, int $incomingStatus): int
    {
        if ($role === RoleType::Admin && $id === 1) {
            return 1;
        }

        return $incomingStatus;
    }

    private function attachPayrollMeta(iterable $staffCollection, RoleType $role): void
    {
        foreach ($staffCollection as $staff) {
            $payrollUser = $this->findPayrollUser($staff, $role);

            $staff->payroll_monthly_salary = $payrollUser?->monthly_salary;
            $staff->payroll_off_days = $payrollUser?->off_days;
            $staff->payroll_panel_start = $payrollUser?->panel_start;
            $staff->payroll_panel_end = $payrollUser?->panel_end;
            $staff->payroll_order_start = $payrollUser?->order_start;
            $staff->payroll_order_end = $payrollUser?->order_end;
        }
    }

    private function findPayrollUser(Authenticatable $staff, RoleType $role): ?User
    {
        $query = User::query()->where('role', $role->value);

        if (! empty($staff->email)) {
            $query->where('email', $staff->email);
        } elseif (! empty($staff->phone)) {
            $query->where('phone', $staff->phone);
        } else {
            $query->where('name', $staff->name);
        }

        return $query->first();
    }

    private function syncPayrollUserFromStaff(Authenticatable $staff, RoleType $role, array $data): void
    {
        $guard = match ($role) {
            RoleType::Admin => 'admin',
            RoleType::Manager => 'manager',
            RoleType::Employee => 'employee',
        };

        $payrollUser = $this->staffUserResolver->resolveOrCreateByStaff($staff, $guard);

        if (array_key_exists('start_time', $data)) {
            $payrollUser->start_time = $this->parseTime($data['start_time']);
        }

        if (array_key_exists('end_time', $data)) {
            $payrollUser->end_time = $this->parseTime($data['end_time']);
        }

        if (array_key_exists('panel_start', $data)) {
            $payrollUser->panel_start = $this->parseTime($data['panel_start']);
        }

        if (array_key_exists('panel_end', $data)) {
            $payrollUser->panel_end = $this->parseTime($data['panel_end']);
        }

        if (array_key_exists('order_start', $data)) {
            $payrollUser->order_start = $this->parseTime($data['order_start']);
        }

        if (array_key_exists('order_end', $data)) {
            $payrollUser->order_end = $this->parseTime($data['order_end']);
        }

        if (array_key_exists('monthly_salary', $data) && $data['monthly_salary'] !== null && $data['monthly_salary'] !== '') {
            $payrollUser->monthly_salary = (float) $data['monthly_salary'];
        }

        if (array_key_exists('off_days', $data)) {
            $payrollUser->off_days = $this->normalizeOffDays($data['off_days']);
        }

        $payrollUser->save();
    }

    private function normalizeOffDays(?string $offDays): ?string
    {
        if (! $offDays) {
            return null;
        }

        $dayList = collect(explode(',', $offDays))
            ->map(fn (string $day): string => trim($day))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return empty($dayList) ? null : implode(',', $dayList);
    }
}
