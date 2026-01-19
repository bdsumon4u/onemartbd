<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\OrderAssign;

class OrderAssignmentService
{
    /**
     * @return list<int>
     */
    public function parseCommaSeparatedIds(?string $ids): array
    {
        if (! $ids) {
            return [];
        }

        return collect(explode(',', $ids))
            ->map(fn ($value) => (int) trim((string) $value))
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Assign orders to a single employee, updating existing assignments and inserting missing ones.
     *
     * @param  list<int>  $orderIds
     */
    public function assignOrdersToEmployee(array $orderIds, int $employeeId): void
    {
        $orderIds = collect($orderIds)
            ->map(fn ($value) => (int) $value)
            ->filter(fn ($value) => $value > 0)
            ->unique()
            ->values()
            ->all();

        if (count($orderIds) === 0) {
            return;
        }

        $now = now();

        $existingOrderIds = OrderAssign::query()
            ->whereIn('order_id', $orderIds)
            ->pluck('order_id')
            ->map(fn ($value) => (int) $value)
            ->all();

        OrderAssign::query()
            ->whereIn('order_id', $orderIds)
            ->update([
                'employee_id' => $employeeId,
                'updated_at' => $now,
            ]);

        $missingOrderIds = array_values(array_diff($orderIds, $existingOrderIds));
        if (count($missingOrderIds) === 0) {
            return;
        }

        $rows = array_map(static fn (int $orderId): array => [
            'order_id' => $orderId,
            'employee_id' => $employeeId,
            'created_at' => $now,
            'updated_at' => $now,
        ], $missingOrderIds);

        OrderAssign::query()->insert($rows);
    }

    /**
     * Assign a newly created order to an employee.
     *
     * If a preferred employee is provided, it will be used. Otherwise a random active employee is chosen.
     *
     * @return array{0: int|null, 1: string}
     */
    public function assignNewOrderToEmployee(int $orderId, ?int $preferredEmployeeId = null): array
    {
        if ($orderId <= 0) {
            return [null, ''];
        }

        if ($preferredEmployeeId && $preferredEmployeeId > 0) {
            $employee = Employee::query()->select('id', 'name')->find($preferredEmployeeId);
            if (! $employee) {
                return [null, ''];
            }

            OrderAssign::query()->updateOrCreate(
                ['order_id' => $orderId],
                ['employee_id' => (int) $employee->id]
            );

            return [(int) $employee->id, (string) $employee->name];
        }

        $employees = Employee::query()->where('status', 1)->pluck('name', 'id')->toArray();
        if (count($employees) === 0) {
            return [null, ''];
        }

        $employeeId = (int) array_rand($employees);

        OrderAssign::query()->updateOrCreate(
            ['order_id' => $orderId],
            ['employee_id' => $employeeId]
        );

        return [$employeeId, (string) ($employees[$employeeId] ?? '')];
    }
}
