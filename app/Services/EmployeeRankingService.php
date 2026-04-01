<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\MonthlyPayroll;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EmployeeRankingService
{
    /**
     * @return Collection<int,object>
     */
    public function monthlyOrderConfirmRanking(int $month, int $year): Collection
    {
        return Order::query()
            ->join('order_assigns', 'order_assigns.order_id', '=', 'orders.id')
            ->join('employees', 'employees.id', '=', 'order_assigns.employee_id')
            ->selectRaw('employees.id as employee_id, employees.name, COUNT(orders.id) as confirmed_orders')
            ->whereMonth('orders.confirmed_at', $month)
            ->whereYear('orders.confirmed_at', $year)
            ->where('orders.status', OrderStatus::Confirmed->value)
            ->groupBy('employees.id', 'employees.name')
            ->orderByDesc('confirmed_orders')
            ->get();
    }

    /**
     * @return Collection<int,object>
     */
    public function payrollPerformanceRanking(int $month, int $year): Collection
    {
        $adminType = addslashes(Admin::class);
        $managerType = addslashes(Manager::class);
        $employeeType = addslashes(Employee::class);

        return MonthlyPayroll::query()
            ->leftJoin('admins', function ($join) use ($adminType): void {
                $join->on('admins.id', '=', 'monthly_payrolls.staff_id')
                    ->where('monthly_payrolls.staff_type', '=', $adminType);
            })
            ->leftJoin('managers', function ($join) use ($managerType): void {
                $join->on('managers.id', '=', 'monthly_payrolls.staff_id')
                    ->where('monthly_payrolls.staff_type', '=', $managerType);
            })
            ->leftJoin('employees', function ($join) use ($employeeType): void {
                $join->on('employees.id', '=', 'monthly_payrolls.staff_id')
                    ->where('monthly_payrolls.staff_type', '=', $employeeType);
            })
            ->selectRaw(
                'monthly_payrolls.staff_id as id,
                COALESCE(admins.name, managers.name, employees.name) as name,
                CASE
                    WHEN monthly_payrolls.staff_type = ? THEN 1
                    WHEN monthly_payrolls.staff_type = ? THEN 2
                    ELSE 3
                END as role,
                monthly_payrolls.present_days,
                monthly_payrolls.off_day_presents,
                monthly_payrolls.overtime_amount,
                monthly_payrolls.hazira_bonus_amount,
                monthly_payrolls.xsell_bonus_amount,
                monthly_payrolls.net_salary',
                [Admin::class, Manager::class]
            )
            ->where('monthly_payrolls.month', $month)
            ->where('monthly_payrolls.year', $year)
            ->orderByDesc('monthly_payrolls.net_salary')
            ->get();
    }

    /**
     * @return array<string,mixed>
     */
    public function summary(int $month, int $year): array
    {
        $activeStaffCount = (int) Admin::query()->where('status', 1)->count()
            + (int) Manager::query()->where('status', 1)->count()
            + (int) Employee::query()->where('status', 1)->count();

        $totalConfirmed = Order::query()
            ->whereMonth('confirmed_at', $month)
            ->whereYear('confirmed_at', $year)
            ->where('status', OrderStatus::Confirmed->value)
            ->count();

        $adminType = addslashes(Admin::class);
        $managerType = addslashes(Manager::class);
        $employeeType = addslashes(Employee::class);

        $topPerformer = DB::table('monthly_payrolls')
            ->leftJoin('admins', function ($join) use ($adminType): void {
                $join->on('admins.id', '=', 'monthly_payrolls.staff_id')
                    ->where('monthly_payrolls.staff_type', '=', $adminType);
            })
            ->leftJoin('managers', function ($join) use ($managerType): void {
                $join->on('managers.id', '=', 'monthly_payrolls.staff_id')
                    ->where('monthly_payrolls.staff_type', '=', $managerType);
            })
            ->leftJoin('employees', function ($join) use ($employeeType): void {
                $join->on('employees.id', '=', 'monthly_payrolls.staff_id')
                    ->where('monthly_payrolls.staff_type', '=', $employeeType);
            })
            ->where('monthly_payrolls.month', $month)
            ->where('monthly_payrolls.year', $year)
            ->orderByDesc('monthly_payrolls.net_salary')
            ->selectRaw('COALESCE(admins.name, managers.name, employees.name) as name, monthly_payrolls.net_salary')
            ->first();

        return [
            'active_staff_count' => $activeStaffCount,
            'total_confirmed_orders' => $totalConfirmed,
            'top_performer' => $topPerformer,
        ];
    }
}
