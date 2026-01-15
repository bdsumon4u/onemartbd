<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        $today = Date::today();

        $top_cities = $this->getTopCities();
        $top_sell = $this->getTopSell();
        $last_order = DB::table('orders')->latest('id')->value('created_at');

        $isAdmin = Auth::guard('admin')->check();
        $isManager = Auth::guard('manager')->check();
        $isEmployee = Auth::guard('employee')->check();

        if ($isAdmin || $isManager) {
            $data = $this->buildAdminOrManagerData($today, $isAdmin, $isManager);
        } elseif ($isEmployee) {
            $data = $this->buildEmployeeData($today, (int) Auth::guard('employee')->id());
        } else {
            $data = [];
        }

        return view('backEnd.admin.dashboard', compact('data', 'top_cities', 'top_sell', 'last_order'));
    }

    private function getTopCities(): Collection
    {
        $topCities = Order::query()
            ->whereNotNull('courier_city_id')
            ->select('courier_city_id', DB::raw('count(*) as total'))
            ->groupBy('courier_city_id')
            ->orderByDesc('total')
            ->get();

        $cityIds = $topCities->pluck('courier_city_id')->filter()->unique()->values();
        $cityNamesById = DB::table('pathao_cities')
            ->whereIn('parent_id', $cityIds)
            ->pluck('city_name', 'parent_id');

        return $topCities->map(function ($row) use ($cityNamesById) {
            $row->city_name = $cityNamesById[$row->courier_city_id] ?? null;

            return $row;
        });
    }

    private function getTopSell(): Collection
    {
        $topSell = DB::table('order_products')
            ->select('product_id', DB::raw('sum(qty) as total'))
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $productIds = $topSell->pluck('product_id')->filter()->unique()->values();
        $productNamesById = DB::table('products')->whereIn('id', $productIds)->pluck('name', 'id');

        return $topSell->map(function ($row) use ($productNamesById) {
            $row->product_name = $productNamesById[$row->product_id] ?? null;

            return $row;
        });
    }

    private function buildAdminOrManagerData($today, bool $isAdmin, bool $isManager): array
    {
        $data = [];

        $data['total_revenue'] = DB::table('orders')->where('status', 1)->sum('total');
        $data['total_customer'] = DB::table('users')->count();
        $data['total_product'] = DB::table('products')->count();

        $data['employees'] = DB::table('employees')
            ->select('id', 'name', 'status', 'last_seen', 'last_login_ip')
            ->whereNotNull('last_seen')
            ->where('status', 1)
            ->get();

        $data['admins'] = DB::table('admins')
            ->select('id', 'name', 'status', 'last_seen', 'last_login_ip')
            ->whereNotNull('last_seen')
            ->where([['status', 1], ['id', '!=', 1]])
            ->get();

        $data['managers'] = DB::table('managers')
            ->select('id', 'name', 'status', 'last_seen', 'last_login_ip')
            ->whereNotNull('last_seen')
            ->where('status', 1)
            ->get();

        $staffCounts = DB::selectOne(
            'select '
            .'(select count(*) from admins) as admins_count, '
            .'(select count(*) from employees) as employees_count, '
            .'(select count(*) from managers) as managers_count'
        );

        $adminsCount = (int) ($staffCounts->admins_count ?? 0);
        $employeesCount = (int) ($staffCounts->employees_count ?? 0);
        $managersCount = (int) ($staffCounts->managers_count ?? 0);

        if ($isAdmin) {
            $data['total_staff'] = $adminsCount + $employeesCount + $managersCount - 1;
        } elseif ($isManager) {
            $data['total_staff'] = $employeesCount + $managersCount - 2;
        }

        $data['recent_orders'] = DB::table('orders')
            ->select('id', 'order_date', 'customer_name', 'customer_phone', 'total', 'status')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $ordersNotDeleted = DB::table('orders')->whereNull('deleted_at');
        $this->fillCountsByStatusGrouped($data, $ordersNotDeleted, $this->statusKey('total'), 'total_order');

        $this->fillAdminTodayCounts($data, $today);

        return $data;
    }

    private function buildEmployeeData($today, int $employeeId): array
    {
        $data = [];

        $data['recent_orders'] = DB::table('orders')
            ->leftJoin('order_assigns', 'order_assigns.order_id', 'orders.id')
            ->where('order_assigns.employee_id', $employeeId)
            ->select('orders.id', 'orders.order_date', 'orders.customer_name', 'orders.customer_phone', 'orders.total', 'orders.status')
            ->orderByDesc('orders.id')
            ->limit(10)
            ->get();

        $assignedBase = DB::table('order_assigns')
            ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
            ->where('order_assigns.employee_id', $employeeId);

        $data['total_order'] = DB::table('order_assigns')->where('employee_id', $employeeId)->count();
        $this->fillCountsByStatusGrouped($data, $assignedBase, $this->statusKey('total'), null, 'orders.status');

        $assignedToday = (clone $assignedBase)->whereDate('orders.order_date', $today);
        $data['today_all_orders'] = (clone $assignedToday)->count();
        $employeeTodayKeys = $this->statusKey('today');
        unset($employeeTodayKeys[12]);
        $this->fillCountsByStatusGrouped($data, $assignedToday, $employeeTodayKeys, null, 'orders.status');

        return $data;
    }

    /**
     * @param  array<int, string>  $statusToKey
     */
    private function fillCountsByStatusGrouped(array &$data, Builder $baseQuery, array $statusToKey, ?string $totalKey, string $statusColumn = 'status'): void
    {
        $counts = (clone $baseQuery)
            ->select($statusColumn, DB::raw('count(*) as total'))
            ->groupBy($statusColumn)
            ->pluck('total', $statusColumn);

        if ($totalKey !== null) {
            $data[$totalKey] = (int) $counts->sum();
        }

        foreach ($statusToKey as $status => $key) {
            $data[$key] = (int) ($counts[$status] ?? 0);
        }
    }

    private function fillAdminTodayCounts(array &$data, $today): void
    {
        $statusToKey = $this->statusKey('today');
        $selectParts = ['COUNT(*) as total_all'];

        foreach ($statusToKey as $status => $key) {
            $condition = "status = {$status}";
            if (in_array($status, [12, 13], true)) {
                $condition .= ' AND deleted_at IS NULL';
            }

            $selectParts[] = "SUM(CASE WHEN {$condition} THEN 1 ELSE 0 END) as s{$status}";
        }

        $row = DB::table('orders')
            ->whereDate('order_date', $today)
            ->selectRaw(implode(', ', $selectParts))
            ->first();

        $data['today_all_orders'] = (int) ($row->total_all ?? 0);
        foreach ($statusToKey as $status => $key) {
            $col = 's'.$status;
            $data[$key] = (int) ($row->{$col} ?? 0);
        }
    }

    /**
     * @return array<int, string>
     */
    private function statusKey(string $prefix): array
    {
        $suffixByStatus = [
            0 => 'hold',
            1 => 'deliver',
            2 => 'process',
            3 => 'pend_pay',
            4 => 'cancel',
            5 => 'pending_invoice',
            6 => 'on_delivery',
            7 => 'pending_return',
            8 => 'courier_hold',
            9 => 'nr_1',
            10 => 'invoiced',
            11 => 'return',
            12 => 'incomplete',
            13 => 'confirmed',
            14 => 'stock_out',
            15 => 'partial_delivery',
            16 => 'lost',
        ];

        $keys = [];
        foreach ($suffixByStatus as $status => $suffix) {
            $keys[$status] = $prefix.'_'.$suffix.'_orders';
        }

        return $keys;
    }
}
