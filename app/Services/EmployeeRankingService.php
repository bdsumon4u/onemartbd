<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\MonthlyPayroll;
use App\Models\Order;
use App\Models\User;
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
        return MonthlyPayroll::query()
            ->join('users', 'users.id', '=', 'monthly_payrolls.user_id')
            ->select([
                'users.id',
                'users.name',
                'users.role',
                'monthly_payrolls.present_days',
                'monthly_payrolls.off_day_presents',
                'monthly_payrolls.overtime_amount',
                'monthly_payrolls.hazira_bonus_amount',
                'monthly_payrolls.xsell_bonus_amount',
                'monthly_payrolls.net_salary',
            ])
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
        $activeStaffCount = User::query()
            ->whereIn('role', [1, 2, 3])
            ->where('status', 1)
            ->count();

        $totalConfirmed = Order::query()
            ->whereMonth('confirmed_at', $month)
            ->whereYear('confirmed_at', $year)
            ->where('status', OrderStatus::Confirmed->value)
            ->count();

        $topPerformer = DB::table('monthly_payrolls')
            ->join('users', 'users.id', '=', 'monthly_payrolls.user_id')
            ->where('monthly_payrolls.month', $month)
            ->where('monthly_payrolls.year', $year)
            ->orderByDesc('monthly_payrolls.net_salary')
            ->select('users.name', 'monthly_payrolls.net_salary')
            ->first();

        return [
            'active_staff_count' => $activeStaffCount,
            'total_confirmed_orders' => $totalConfirmed,
            'top_performer' => $topPerformer,
        ];
    }
}
