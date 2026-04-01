<?php

namespace App\Services;

use App\Enums\RoleType;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class StaffUserResolver
{
    public function __construct(private ActingUserContextResolver $actingUserContextResolver) {}

    public function resolveAuthenticatedStaffUser(): ?User
    {
        [$staff, $guard] = $this->actingUserContextResolver->resolve();

        if (! $staff || ! $guard) {
            return null;
        }

        return $this->resolveOrCreateByStaff($staff, $guard);
    }

    public function resolveOrCreateByStaff(Authenticatable $staff, string $guard): User
    {
        $role = match ($guard) {
            'admin' => RoleType::Admin->value,
            'manager' => RoleType::Manager->value,
            default => RoleType::Employee->value,
        };

        $query = User::query()->where('role', $role);

        if (! empty($staff->email)) {
            $query->where('email', $staff->email);
        } elseif (! empty($staff->phone)) {
            $query->where('phone', $staff->phone);
        } else {
            $query->where('name', $staff->name);
        }

        $existing = $query->first();

        if ($existing) {
            return tap($existing, function (User $user) use ($staff): void {
                $user->forceFill([
                    'name' => $staff->name,
                    'email' => $staff->email ?: $user->email,
                    'phone' => $staff->phone ?: $user->phone,
                    'status' => $staff->status ?? 1,
                ])->save();
            });
        }

        return User::query()->create([
            'name' => $staff->name,
            'email' => $staff->email ?: uniqid($guard.'_').'@staff.local',
            'phone' => $staff->phone,
            'password' => $staff->password,
            'status' => $staff->status ?? 1,
            'role' => $role,
        ]);
    }

    public function syncAllStaffUsers(): void
    {
        Admin::query()->where('status', 1)->get()->each(function (Admin $admin): void {
            $this->resolveOrCreateByStaff($admin, 'admin');
        });

        Manager::query()->where('status', 1)->get()->each(function (Manager $manager): void {
            $this->resolveOrCreateByStaff($manager, 'manager');
        });

        Employee::query()->where('status', 1)->get()->each(function (Employee $employee): void {
            $this->resolveOrCreateByStaff($employee, 'employee');
        });
    }
}
