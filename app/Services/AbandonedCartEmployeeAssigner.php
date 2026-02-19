<?php

namespace App\Services;

use App\Models\AbandonedCart;
use App\Models\Employee;
use App\Models\UserProducts;
use Illuminate\Support\Facades\Date;

class AbandonedCartEmployeeAssigner
{
    public function assignEmployeeToAbandonedCart(AbandonedCart $cart): ?int
    {
        $items = json_decode((string) $cart->abandoned_item, true);

        if (is_array($items) && count($items) === 1) {
            $productId = $items[0]['product_id'] ?? null;
            if ($productId) {
                $employeeId = $this->assignProductSpecificEmployee($productId);
                if ($employeeId) {
                    $cart->update(['employee_id' => $employeeId]);

                    return $employeeId;
                }
            }
        }

        $employeeId = $this->assignRandomEmployee();
        if ($employeeId) {
            $cart->update(['employee_id' => $employeeId]);
        }

        return $employeeId;
    }

    private function assignProductSpecificEmployee(int $productId): ?int
    {
        $productEmployees = UserProducts::join('employees', 'employees.id', 'user_products.user_id')
            ->where('user_products.product_id', $productId)
            ->where('employees.status', 1)
            ->pluck('employees.id');

        if ($productEmployees->isNotEmpty()) {
            return $productEmployees->random();
        }

        return null;
    }

    private function assignRandomEmployee(): ?int
    {
        $currentTime = Date::now()->toTimeString();
        $employees = Employee::where('status', 1)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->pluck('id');

        return $employees->isNotEmpty() ? $employees->random() : null;
    }
}
