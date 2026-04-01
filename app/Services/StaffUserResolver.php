<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use Illuminate\Support\Collection;

class StaffUserResolver
{
    public function __construct(private ActingUserContextResolver $actingUserContextResolver) {}

    public function resolveAuthenticatedStaffUser(): Admin|Manager|Employee|null
    {
        [$staff, $guard] = $this->actingUserContextResolver->resolve();

        if (! $staff || ! $guard) {
            return null;
        }

        $this->attachStaffMeta($staff, $guard);

        return $staff;
    }

    /**
     * @return Collection<int,Admin|Manager|Employee>
     */
    public function allActiveStaff(): Collection
    {
        $admins = Admin::query()->where('status', 1)->orderBy('name')->get();
        $managers = Manager::query()->where('status', 1)->orderBy('name')->get();
        $employees = Employee::query()->where('status', 1)->orderBy('name')->get();

        $admins->each(fn (Admin $admin): Admin => $this->attachStaffMeta($admin, 'admin'));
        $managers->each(fn (Manager $manager): Manager => $this->attachStaffMeta($manager, 'manager'));
        $employees->each(fn (Employee $employee): Employee => $this->attachStaffMeta($employee, 'employee'));

        return collect()
            ->concat($admins)
            ->concat($managers)
            ->concat($employees)
            ->sortBy('name')
            ->values();
    }

    public function resolveByStaffKey(string $staffKey): Admin|Manager|Employee|null
    {
        [$type, $id] = $this->parseStaffKey($staffKey);

        if (! $type || ! $id) {
            return null;
        }

        $model = match ($type) {
            'admin' => Admin::query()->find($id),
            'manager' => Manager::query()->find($id),
            'employee' => Employee::query()->find($id),
            default => null,
        };

        if (! $model) {
            return null;
        }

        $this->attachStaffMeta($model, $type);

        return $model;
    }

    public function makeStaffKey(string $staffType, int $staffId): string
    {
        return $staffType.':'.$staffId;
    }

    private function attachStaffMeta(Admin|Manager|Employee $staff, string $staffType): Admin|Manager|Employee
    {
        $staff->setAttribute('staff_type', $staffType);
        $staff->setAttribute('staff_id', (int) $staff->getAuthIdentifier());
        $staff->setAttribute('staff_key', $this->makeStaffKey($staffType, (int) $staff->getAuthIdentifier()));

        return $staff;
    }

    /**
     * @return array{0:string|null,1:int|null}
     */
    private function parseStaffKey(string $staffKey): array
    {
        $parts = explode(':', $staffKey);

        if (count($parts) !== 2) {
            return [null, null];
        }

        $type = trim((string) $parts[0]);
        $id = (int) $parts[1];

        if (! in_array($type, ['admin', 'manager', 'employee'], true) || $id <= 0) {
            return [null, null];
        }

        return [$type, $id];
    }
}
