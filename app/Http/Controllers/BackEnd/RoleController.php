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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RoleController extends Controller
{
    public function index()
    {
        $data['admin'] = Admin::get();
        $data['manager'] = Manager::get();
        $data['employee'] = Employee::get();

        return view('backEnd.admin.roles.index', compact('data'));
    }

    public function store(StoreRoleRequest $request)
    {
        $validated = $request->validated();
        $role = RoleType::from((int) $validated['role']);

        $this->userModel($role)::create(
            $this->buildPayload($validated, $validated['password'])
        );

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
}
