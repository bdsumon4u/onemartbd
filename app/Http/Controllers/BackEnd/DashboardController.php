<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function dashboard(): View
    {
        $today = Date::today();

        $topSellRange = 'month';
        $top_sell = $this->getTopSell($topSellRange);
        $topSellChart = $this->buildTopSellChart($top_sell);
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

        return view('backEnd.admin.dashboard', compact('data', 'top_sell', 'last_order', 'topSellChart', 'topSellRange'));
    }

    public function topSellFilter(Request $request): JsonResponse
    {
        $range = $request->query('range', 'month');
        $topSell = $this->getTopSell($range);
        $chartData = $this->buildTopSellChart($topSell);

        $items = $topSell->map(function ($row): array {
            return [
                'name' => $row->product_name ?: 'Unknown',
                'total' => (int) $row->total,
            ];
        })->values();

        return response()->json([
            'labels' => $chartData['labels'],
            'totals' => $chartData['totals'],
            'items' => $items,
        ]);
    }

    public function trafficSourceStats(Request $request): JsonResponse
    {
        $range = $request->query('range', 'month');
        $rangeBounds = $this->resolveTopSellRange($range);

        $query = DB::table('utm_visits')
            ->select('source', DB::raw('count(*) as total'))
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->groupBy('source')
            ->orderByDesc('total')
            ->limit(10);

        if ($rangeBounds !== null) {
            $query->whereBetween('created_at', [
                $rangeBounds['start'],
                $rangeBounds['end'],
            ]);
        }

        $stats = $query->get();

        return response()->json($this->formatTrafficSourceStats($stats));
    }

    public function utmMediumStats(Request $request): JsonResponse
    {
        $range = $request->query('range', 'month');
        $rangeBounds = $this->resolveTopSellRange($range);

        $query = DB::table('utm_visits')
            ->select('utm_medium', DB::raw('count(*) as total'))
            ->whereNotNull('utm_medium')
            ->where('utm_medium', '!=', '')
            ->groupBy('utm_medium')
            ->orderByDesc('total')
            ->limit(10);

        if ($rangeBounds !== null) {
            $query->whereBetween('created_at', [
                $rangeBounds['start'],
                $rangeBounds['end'],
            ]);
        }

        $stats = $query->get();

        return response()->json($this->formatUtmStats($stats, 'utm_medium'));
    }

    public function utmCampaignStats(Request $request): JsonResponse
    {
        $range = $request->query('range', 'month');
        $rangeBounds = $this->resolveTopSellRange($range);

        $query = DB::table('utm_visits')
            ->select('utm_campaign', DB::raw('count(*) as total'))
            ->whereNotNull('utm_campaign')
            ->where('utm_campaign', '!=', '')
            ->groupBy('utm_campaign')
            ->orderByDesc('total')
            ->limit(10);

        if ($rangeBounds !== null) {
            $query->whereBetween('created_at', [
                $rangeBounds['start'],
                $rangeBounds['end'],
            ]);
        }

        $stats = $query->get();

        return response()->json($this->formatUtmStats($stats, 'utm_campaign'));
    }

    public function topCitiesStats(Request $request): JsonResponse
    {
        $range = $request->query('range', 'month');
        $rangeBounds = $this->resolveTopSellRange($range);

        $query = Order::query()
            ->whereNotNull('courier_city_id')
            ->select('courier_city_id', DB::raw('count(*) as total'))
            ->groupBy('courier_city_id')
            ->orderByDesc('total')
            ->limit(30);

        if ($rangeBounds !== null) {
            $query->whereBetween('created_at', [
                $rangeBounds['start'],
                $rangeBounds['end'],
            ]);
        }

        $topCities = $query->get();

        $cityIds = $topCities->pluck('courier_city_id')->filter()->unique()->values();
        $cityNamesById = DB::table('pathao_cities')
            ->whereIn('parent_id', $cityIds)
            ->pluck('city_name', 'parent_id');

        $items = $topCities->map(function ($row) use ($cityNamesById) {
            return [
                'label' => $cityNamesById[$row->courier_city_id] ?? 'Unknown',
                'total' => (int) $row->total,
            ];
        });

        $labels = $items->pluck('label')->toArray();
        $totals = $items->pluck('total')->toArray();

        return response()->json([
            'labels' => $labels,
            'totals' => $totals,
            'items' => $items->toArray(),
        ]);
    }

    private function getTopSell(?string $range = null): Collection
    {
        $topSellQuery = DB::table('order_products')
            ->select('order_products.product_id', DB::raw('sum(order_products.qty) as total'));

        $rangeBounds = $this->resolveTopSellRange($range);
        if ($rangeBounds !== null) {
            $topSellQuery
                ->join('orders', 'orders.id', '=', 'order_products.order_id')
                ->whereBetween('orders.created_at', [$rangeBounds['start'], $rangeBounds['end']]);
        }

        $topSell = $topSellQuery
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $productIds = $topSell->pluck('product_id')->filter()->unique()->values();
        $productNamesById = DB::table('products')->whereIn('id', $productIds)->pluck('name', 'id');

        return $topSell->map(function ($row) use ($productNamesById) {
            $row->product_name = $productNamesById[$row->product_id] ?? null;

            return $row;
        });
    }

    private function buildTopSellChart(Collection $topSell): array
    {
        return [
            'labels' => $topSell->map(function ($row): string {
                return $row->product_name ?: 'Unknown';
            })->values()->all(),
            'totals' => $topSell->map(function ($row): int {
                return (int) $row->total;
            })->values()->all(),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $stats
     */
    private function formatTrafficSourceStats(Collection $stats): array
    {
        $labelMap = [
            'facebook' => 'Facebook',
            'google' => 'Google',
            'instagram' => 'Instagram',
            'tiktok' => 'TikTok',
            'youtube' => 'YouTube',
            'linkedin' => 'LinkedIn',
            'twitter' => 'X/Twitter',
            'bing' => 'Bing',
            'yahoo' => 'Yahoo',
            'direct' => 'Organic/Direct',
            'referral' => 'Referral',
        ];

        $items = $stats->map(function ($row) use ($labelMap): array {
            $source = strtolower((string) ($row->source ?? ''));
            $label = $labelMap[$source] ?? Str::title(str_replace(['-', '_'], ' ', $source));

            return [
                'label' => $label,
                'source' => $source,
                'total' => (int) ($row->total ?? 0),
            ];
        })->filter(function (array $item): bool {
            return $item['total'] > 0;
        })->values();

        return [
            'labels' => $items->pluck('label')->values()->all(),
            'totals' => $items->pluck('total')->values()->all(),
            'items' => $items->map(function (array $item): array {
                return [
                    'label' => $item['label'],
                    'total' => $item['total'],
                ];
            })->values()->all(),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $stats
     */
    private function formatUtmStats(Collection $stats, string $field): array
    {
        $items = $stats->map(function ($row) use ($field): array {
            $value = (string) ($row->{$field} ?? '');
            $label = Str::title(str_replace(['-', '_'], ' ', $value));

            return [
                'label' => $label ?: 'Unknown',
                'value' => $value,
                'total' => (int) ($row->total ?? 0),
            ];
        })->filter(function (array $item): bool {
            return $item['total'] > 0;
        })->values();

        return [
            'labels' => $items->pluck('label')->values()->all(),
            'totals' => $items->pluck('total')->values()->all(),
            'items' => $items->map(function (array $item): array {
                return [
                    'label' => $item['label'],
                    'total' => $item['total'],
                ];
            })->values()->all(),
        ];
    }

    /**
     * @return array{start: \Illuminate\Support\Carbon, end: \Illuminate\Support\Carbon}|null
     */
    private function resolveTopSellRange(?string $range): ?array
    {
        if ($range === null) {
            return null;
        }

        $range = strtolower(trim($range));
        $end = Date::now();

        return match ($range) {
            'today' => ['start' => Date::today(), 'end' => $end],
            '3days' => ['start' => Date::now()->subDays(2)->startOfDay(), 'end' => $end],
            'week' => ['start' => Date::now()->subDays(6)->startOfDay(), 'end' => $end],
            'month' => ['start' => Date::now()->subDays(29)->startOfDay(), 'end' => $end],
            '3months' => ['start' => Date::now()->subDays(89)->startOfDay(), 'end' => $end],
            '6months' => ['start' => Date::now()->subDays(179)->startOfDay(), 'end' => $end],
            default => null,
        };
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
