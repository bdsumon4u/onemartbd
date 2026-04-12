<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderAssign;
use App\Models\UserProducts;

class OrderAssignmentService
{
    public function __construct(
        private ActiveOrderEmployeeResolver $activeOrderEmployeeResolver,
    ) {}

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

    /**
     * Match storefront assignment: single product → employees linked to that product (random);
     * otherwise → active employees in order time window (random), or any active employee if none in window.
     */
    public function assignEmployeeLikeStorefront(Order $order): ?int
    {
        $order->loadMissing('get_products');

        $distinctProductIds = $order->get_products->pluck('product_id')->unique()->filter()->values();
        if ($distinctProductIds->count() === 1) {
            $productId = (int) $distinctProductIds->first();
            $productEmployees = UserProducts::query()
                ->join('employees', 'employees.id', '=', 'user_products.user_id')
                ->where('user_products.product_id', $productId)
                ->where('employees.status', 1)
                ->pluck('employees.name', 'employees.id')
                ->toArray();

            if ($productEmployees !== []) {
                $employeeId = (int) array_rand($productEmployees);
                OrderAssign::query()->updateOrCreate(
                    ['order_id' => $order->id],
                    ['employee_id' => $employeeId]
                );

                return $employeeId;
            }
        }

        $employees = $this->activeOrderEmployeeResolver->activeEmployeeNameMap()->toArray();
        if ($employees === []) {
            $employees = Employee::query()->where('status', 1)->pluck('name', 'id')->toArray();
        }

        if ($employees === []) {
            return null;
        }

        $employeeId = (int) array_rand($employees);
        OrderAssign::query()->updateOrCreate(
            ['order_id' => $order->id],
            ['employee_id' => $employeeId]
        );

        return $employeeId;
    }
}
