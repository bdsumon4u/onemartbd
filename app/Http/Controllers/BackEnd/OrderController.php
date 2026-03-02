<?php

namespace App\Http\Controllers\BackEnd;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Models\Courier;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderTransaction;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\SmsSetting;
use App\Models\User;
use App\Models\UtmVisit;
use App\Services\ActingUserContextResolver;
use App\Services\OrderAssignmentService;
use App\Services\OrderCourierService;
use App\Services\OrderCustomerNotificationService;
use App\Services\OrderForwardingService;
use App\Services\OrderInvoiceIdGenerator;
use App\Services\OrderNoteService;
use App\Services\OrderTransactionService;
use App\Services\WhatsappServices;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function __construct(
        protected ActingUserContextResolver $actingUserContextResolver,
        protected OrderAssignmentService $orderAssignmentService,
        protected OrderCourierService $orderCourierService,
        protected OrderCustomerNotificationService $orderCustomerNotificationService,
        protected OrderTransactionService $orderTransactionService,
        protected OrderNoteService $orderNoteService,
        protected WhatsappServices $WpServices,
        protected OrderForwardingService $orderForwardingService,
        protected OrderInvoiceIdGenerator $invoiceIdGenerator,
    ) {}

    public function index(Request $request)
    {
        $paginate = (int) ($request->input('paginate') ?: 10);

        $query = $request->input('query') ?? null;
        $status = $request->input('status');
        $sts = $status ? (OrderStatus::labelsToValues()[$status] ?? null) : null;

        if (Auth::guard('admin')->check() || Auth::guard('manager')->check()) {
            $data = [
                'couriers' => DB::table('couriers')->where('status', 1)->pluck('courier_name', 'id'),
                'shippings' => DB::table('shipping_methods')->where('status', 1)->pluck('type', 'id'),
                'employees' => DB::table('employees')->where('status', 1)->pluck('name', 'id'),
            ];

            $totalsQuery = Order::query()->whereNull('deleted_at');
            $totalsQuery = $this->applyTotalsFilters($totalsQuery, $request);

            if ($request->input('courier_id')) {
                $data['courier_id'] = $request->input('courier_id');
            }

            if ($request->input('shipping_id')) {
                $data['shipping_id'] = $request->input('shipping_id');
            }

            $totalsByStatus = (clone $totalsQuery)
                ->selectRaw('status, COUNT(*) as order_count, COALESCE(SUM(COALESCE(sub_total, 0) - COALESCE(discount, 0)), 0) as total_amount')
                ->groupBy('status')
                ->get()
                ->keyBy('status');

            $data['total_trash_order'] = Order::onlyTrashed()->count();

            $data['total_order'] = (int) $totalsByStatus->sum('order_count');
            $data['total_order_amount'] = (float) $totalsByStatus->sum('total_amount');

            $this->fillStatusTotals($data, $totalsByStatus);

            $ordersQuery = Order::query();
            $ordersQuery = $this->applyOrdersFilters($ordersQuery, $request, $sts);

            $data['count'] = (clone $ordersQuery)->count();
            $data['orders'] = $ordersQuery->with($this->orderIndexWithRelations())
                ->select($this->orderIndexSelectColumns())
                ->orderBy('id', 'desc')->paginate($paginate);
            $data['orders']->appends([
                'paginate' => $paginate,
                'query' => $request->input('query'),
                'status' => $request->input('status'),
                'custom_range' => $request->input('custom_range'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'courier_id' => $request->input('courier_id'),
                'shipping_id' => $request->input('shipping_id'),
                'product_id' => $request->input('product_id'),
                'employee_id' => $request->input('employee_id'),
            ]);
        } elseif (Auth::guard('employee')->check()) {
            $employeeId = Auth::guard('employee')->id();

            $data = [
                'couriers' => DB::table('couriers')->where('status', 1)->pluck('courier_name', 'id'),
                'shippings' => DB::table('shipping_methods')->where('status', 1)->pluck('type', 'id'),
            ];

            if ($request->input('courier_id')) {
                $data['courier_id'] = $request->input('courier_id');
            }

            if ($request->input('shipping_id')) {
                $data['shipping_id'] = $request->input('shipping_id');
            }

            $totalsQuery = Order::query()->whereHas('get_assigned', function ($qry) use ($employeeId): void {
                $qry->where('employee_id', $employeeId);
            });
            $totalsQuery = $this->applyTotalsFilters($totalsQuery, $request);

            $totalsByStatus = (clone $totalsQuery)
                ->selectRaw('status, COUNT(*) as order_count, COALESCE(SUM(COALESCE(sub_total, 0) - COALESCE(discount, 0)), 0) as total_amount')
                ->groupBy('status')
                ->get()
                ->keyBy('status');

            $data['total_trash_order'] = Order::onlyTrashed()->count();

            $data['total_order'] = (int) $totalsByStatus->sum('order_count');
            $data['total_order_amount'] = (float) $totalsByStatus->sum('total_amount');

            $this->fillStatusTotals($data, $totalsByStatus);

            $ordersQuery = Order::when(! $request->input('query'), function ($query) use ($employeeId): void {
                $query->whereHas('get_assigned', function ($qry) use ($employeeId): void {
                    $qry->where('employee_id', $employeeId);
                });
            });

            $ordersQuery = $this->applyOrdersFilters($ordersQuery, $request, $sts, true);

            $data['count'] = (clone $ordersQuery)->count();
            $data['orders'] = $ordersQuery->with($this->orderIndexWithRelations())
                ->select($this->orderIndexSelectColumns())
                ->orderBy('id', 'desc')->paginate($paginate);
            $data['orders']->appends([
                'paginate' => $paginate,
                'query' => $request->input('query'),
                'status' => $request->input('status'),
                'custom_range' => $request->input('custom_range'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'courier_id' => $request->input('courier_id'),
                'shipping_id' => $request->input('shipping_id'),
                'product_id' => $request->input('product_id'),
                'employee_id' => $request->input('employee_id'),
            ]);
        } else {
            $data = [];
        }
        // dd($data['orders']);
        // dd($data);
        $products = Product::orderBy('id', 'desc')->pluck('name', 'id');

        return view('backEnd.admin.orders.index', compact('products', 'data', 'query', 'status', 'sts'));
    }

    private function applyDateRangeFilter(Builder $query, Request $request): Builder
    {
        $customRange = $request->input('custom_range');

        if ($customRange === 'today') {
            $query->whereDate('created_at', \Illuminate\Support\Facades\Date::today());

            return $query;
        }

        if ($customRange === 'yesterday') {
            $query->whereDate('created_at', \Illuminate\Support\Facades\Date::yesterday());

            return $query;
        }

        if ($customRange === 'last_7_days') {
            $query->whereBetween('created_at', [
                \Illuminate\Support\Facades\Date::now()->subDays(6)->startOfDay(),
                \Illuminate\Support\Facades\Date::now()->endOfDay(),
            ]);

            return $query;
        }

        if ($customRange === 'this_month') {
            $query->whereBetween('created_at', [
                \Illuminate\Support\Facades\Date::now()->startOfMonth(),
                \Illuminate\Support\Facades\Date::now()->endOfMonth(),
            ]);

            return $query;
        }

        if ($customRange === 'last_month') {
            $start = \Illuminate\Support\Facades\Date::now()->subMonthNoOverflow()->startOfMonth();
            $end = \Illuminate\Support\Facades\Date::now()->subMonthNoOverflow()->endOfMonth();

            $query->whereBetween('created_at', [$start, $end]);

            return $query;
        }

        if ($customRange === 'last_6_months') {
            $query->whereBetween('created_at', [
                \Illuminate\Support\Facades\Date::now()->subMonthsNoOverflow(5)->startOfMonth(),
                \Illuminate\Support\Facades\Date::now()->endOfMonth(),
            ]);

            return $query;
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [
                \Illuminate\Support\Facades\Date::parse($startDate)->startOfDay(),
                \Illuminate\Support\Facades\Date::parse($endDate)->endOfDay(),
            ]);

            return $query;
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', \Illuminate\Support\Facades\Date::parse($startDate));
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', \Illuminate\Support\Facades\Date::parse($endDate));
        }

        return $query;
    }

    private function applyTotalsFilters(Builder $query, Request $request): Builder
    {
        $query = $this->applyDateRangeFilter($query, $request);

        if ($request->input('courier_id')) {
            $query->where('courier_id', (int) $request->input('courier_id'));
        }

        if ($request->input('shipping_id')) {
            $query->where('shipping_method', (int) $request->input('shipping_id'));
        }

        if ($search = $request->input('query')) {
            $query->where(function ($q) use ($search): void {
                $q->where('customer_phone', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('invoice_id', 'LIKE', "%{$search}%")
                    ->orWhere('ip_address', $search);
            });
        }

        if ($productId = $request->input('product_id')) {
            $query->whereHas('get_products', function ($q) use ($productId): void {
                $q->where('product_id', (int) $productId);
            });
        }

        if ($employeeId = $request->input('employee_id')) {
            $query->whereHas('get_assigned', function ($q) use ($employeeId): void {
                $q->where('employee_id', (int) $employeeId);
            });
        }

        return $query;

    }

    private function applyOrdersFilters(Builder $query, Request $request, $statusValue, bool $includeProductSearch = false): Builder
    {
        $query = $this->applyDateRangeFilter($query, $request);

        if ($statusValue !== null && $statusValue !== '') {
            $query->where('status', (int) $statusValue);
        }

        if ($request->input('courier_id')) {
            $query->where('courier_id', (int) $request->input('courier_id'));
        }

        if ($request->input('shipping_id')) {
            $query->where('shipping_method', (int) $request->input('shipping_id'));
        }

        if ($search = $request->input('query')) {
            $query->where(function ($q) use ($search, $includeProductSearch): void {
                $q->where('customer_phone', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('invoice_id', 'LIKE', "%{$search}%")
                    ->orWhere('ip_address', $search);

                if ($includeProductSearch) {
                    $q->orWhereHas('get_products', function ($p) use ($search): void {
                        $p->join('products', 'products.id', 'order_products.product_id')
                            ->where('name', 'LIKE', "%{$search}%");
                    });
                }
            });
        }

        if ($productId = $request->input('product_id')) {
            $query->whereHas('get_products', function ($q) use ($productId): void {
                $q->where('product_id', (int) $productId);
            });
        }

        if ($employeeId = $request->input('employee_id')) {
            $query->whereHas('get_assigned', function ($q) use ($employeeId): void {
                $q->where('employee_id', (int) $employeeId);
            });
        }

        return $query;
    }

    private function orderIndexWithRelations(): array
    {
        return ['get_products.get_product', 'get_courier', 'get_assigned.get_employee'];
    }

    private function orderIndexSelectColumns(): array
    {
        return [
            'carrybee_consignment_id',
            'source',
            'utm_source',
            'courier_api_response',
            'courier_status_reason',
            'customer_activity',
            'is_fake',
            'invoice_id',
            'customer_name',
            'customer_phone',
            'customer_address',
            'total',
            'order_date',
            'created_at',
            'status',
            'staff_note',
            'courier_note',
            'courier_status',
            'id',
            'ip_address',
            'courier_id',
            'paid',
            'due',
            'pathao_consignment_id',
            'redx_tracking_id',
            'payment_status',
            'shipping_method',
            'handover_date',
            'master_id',
            'slave_id',
            'slave_domain',
            'forwarding_status',
            'forwarding_last_error',
        ];
    }

    private function fillStatusTotals(array &$data, $totalsByStatus): void
    {
        $definitions = [
            0 => ['total_hold_order', 'total_hold_amount'],
            1 => ['total_deliver_order', 'total_deliver_amount'],
            2 => ['total_process_order', 'total_process_amount'],
            3 => ['total_pend_pay_order', 'total_pend_pay_amount'],
            4 => ['total_cancel_order', 'total_cancel_amount'],
            5 => ['total_pending_invoice_order', 'total_pending_invoice_amount'],
            6 => ['total_on_delivery_order', 'total_on_delivery_amount'],
            7 => ['total_pending_return_order', 'total_pending_return_amount'],
            8 => ['total_courier_hold_order', 'total_courier_hold_amount'],
            9 => ['total_nr_1_order', 'total_nr_1_amount'],
            10 => ['total_invoiced_order', 'total_invoiced_amount'],
            11 => ['total_return_order', 'total_return_amount'],
            12 => ['total_incomplete_order', 'total_incomplete_amount'],
            13 => ['total_confirmed_order', 'total_confirmed_amount'],
            14 => ['total_stock_out_order', 'total_stock_out_amount'],
            15 => ['total_partial_delivery_order', 'total_partial_delivery_amount'],
            16 => ['total_lost_order', 'total_lost_amount'],
            17 => ['total_paid_return_order', 'total_paid_return_amount'],
            18 => ['total_exchange_order', 'total_exchange_amount'],
        ];

        foreach ($definitions as $status => [$countKey, $amountKey]) {
            $row = $totalsByStatus->get($status);
            $data[$countKey] = (int) ($row->order_count ?? 0);
            $data[$amountKey] = (float) ($row->total_amount ?? 0);
        }
    }

    public function create()
    {
        $products = Product::pluck('name', 'id');
        $courier = Courier::where('status', 1)->pluck('courier_name', 'id');
        $shipping = ShippingMethod::where('status', 1)->pluck('type', 'id');
        $invoice_id = $this->invoiceIdGenerator->next();

        return view('backEnd.admin.orders.add', compact('products', 'courier', 'invoice_id', 'shipping'));
    }

    public function store(Request $request)
    {
        if (Order::withTrashed()->count() > 0) {
            $invoice_id = Order::withTrashed()->latest('id')->first()->invoice_id;
            $invoice_id = trim((string) $invoice_id, 'INV');
            $invoice_id++;
            $invoice_id = 'INV'.$invoice_id;
        } else {
            $invoice_id = 'INV1';
        }

        // create customer account
        $check_cus = User::where('phone', $request->customer_phone)->first();
        if ($check_cus) {
            $customer_id = $check_cus;
        } else {
            $customer_id = User::create([
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'address' => $request->customer_address,
                'password' => Hash::make($request->customer_phone),
            ]);
        }

        $order_date = \Illuminate\Support\Facades\Date::parse($request->order_date)->format('Y-m-d');
        $inputs = array_merge($request->all(), [
            'invoice_id' => $invoice_id,
            'order_date' => $order_date,
            'customer_id' => $customer_id->id,
            'status' => $request->status,
        ]);

        $order_id = Order::create($inputs);

        $sms = SmsSetting::where('status', $order_id->status)->first();
        // send whatsapp
        if ($sms && $sms->is_whatsapp == 1 && $sms->template_name != null) {
            $this->WpServices->sendOrderWhatsapp($order_id, $sms->template_name, $sms->status);
        }
        // insert products
        foreach ($request->product_id as $key => $item) {

            $attrb = [];
            if ($request->attribute_id) {
                if (array_key_exists($item, $request->attribute_id)) {
                    foreach ($request->attribute_id[$item] as $item2) {
                        $an = Attribute::find($item2)->title;
                        $ain = AttributeItem::find($request->attribute_item_id[$item][$item2][0])->item_title;
                        $attr[0][$an] = $ain;
                    }
                    foreach ($request->attribute_id[$item] as $item2) {
                        $an = $item2;
                        $ain = $request->attribute_item_id[$item][$item2][0];
                        $attr[1][$an] = $ain;
                    }
                    $attrb[0] = json_encode($attr[0]);
                    $attrb[1] = json_encode($attr[1]);
                } else {
                    $attrb = [];
                }
            }
            $price = $request->price[$key] * $request->qty[$key];
            $purchase_cost = Product::where('id', $item)->first()->purchase_cost;
            OrderProduct::create([
                'order_id' => $order_id->id,
                'product_id' => $item,
                'qty' => $request->qty[$key],
                'price' => $request->price[$key],
                'purchase_cost' => $purchase_cost,
                'attributes' => count($attrb) > 0 ? $attrb[0] : null,
                'attribute_ids' => count($attrb) > 0 ? $attrb[1] : null,
            ]);
        }

        if ($request->courier_id == 1) {
            $this->orderCourierService->applyCourierChargeCost(
                $order_id,
                (int) $request->courier_id,
                (int) $request->shipping_area,
            );
        } elseif ($request->courier_id == 2) {
            $this->orderCourierService->applyCourierChargeCost(
                $order_id,
                (int) $request->courier_id,
                (int) $request->shipping_area,
            );
        } elseif ($request->courier_id == 3) {
            $this->orderCourierService->applyCourierChargeCost(
                $order_id,
                (int) $request->courier_id,
                (int) $request->shipping_area,
            );
        }

        [$user, $createdBy] = $this->actingUserContextResolver->resolve();
        if (! $user || ! $createdBy) {
            return back()->with('warning', 'Something Went Wrong');
        }

        $preferredEmployeeId = Auth::guard('employee')->check() ? (int) Auth::guard('employee')->id() : null;
        [$assignedEmployeeId, $employeeName] = $this->orderAssignmentService->assignNewOrderToEmployee(
            (int) $order_id->id,
            $preferredEmployeeId,
        );

        $logged = $this->orderTransactionService->logFromTemplateForActor(
            (int) $order_id->id,
            'transaction_texts.new_order',
            ['{employee_name}' => $employeeName],
            $createdBy,
            (int) $user->id,
            (string) $user->name,
            $assignedEmployeeId,
        );

        if (! $logged) {
            return back()->with('warning', 'Something Went Wrong');
        }

        // Forward to master (if configured) after order and products are created
        $this->orderForwardingService->forwardIfConfigured($order_id);

        return $this->redirectToOrders('success', 'Order Created Successfully');
    }

    public function edit($id)
    {
        $products = Product::pluck('name', 'id');

        $data = Order::with('get_transactions', 'get_products.get_product', 'get_note_history', 'get_customer')->find($id);

        if (! $data) {
            return back()->with('error', 'Order Not Found');
        }

        $courier = Courier::where('status', 1)->pluck('courier_name', 'id');

        [$courier_city, $courier_zone] = $this->orderCourierService->cityAndZoneOptionsForOrder($data);

        return view('backEnd.admin.orders.edit', compact('data', 'products', 'courier', 'courier_city', 'courier_zone'));
    }

    public function update(Request $request, $id)
    {
        if ($request->product_id) {
            $order_date = \Illuminate\Support\Facades\Date::parse($request->order_date)->format('Y-m-d');
            $inputs = array_merge($request->all(), [
                'order_date' => $order_date,
                // 'status' => $request->status ?? $request->old_status,
            ]);

            $order_id = Order::find($id);

            $order_id->update($inputs);

            OrderProduct::where('order_id', $id)->delete();
            foreach ($request->product_id as $key => $item) {
                $attrb = [];
                if ($request->attribute_id) {
                    if (array_key_exists($item, $request->attribute_id)) {
                        foreach ($request->attribute_id[$item] as $item2) {
                            $an = Attribute::find($item2)->title;
                            $ain = AttributeItem::find($request->attribute_item_id[$item][$item2][0])->item_title;
                            $attr[0][$an] = $ain;
                        }
                        foreach ($request->attribute_id[$item] as $item2) {
                            $an = $item2;
                            $ain = $request->attribute_item_id[$item][$item2][0];
                            $attr[1][$an] = $ain;
                        }
                        $attrb[0] = json_encode($attr[0]);
                        $attrb[1] = json_encode($attr[1]);
                    } else {
                        $attrb = [];
                    }
                }

                $price = $request->price[$key] * $request->qty[$key];
                OrderProduct::create([
                    'order_id' => $id,
                    'product_id' => $item,
                    'qty' => $request->qty[$key],
                    'price' => $request->price[$key],
                    'purchase_cost' => Product::where('id', $item)->first()->purchase_cost,
                    'attributes' => count($attrb) > 0 ? $attrb[0] : null,
                    'attribute_ids' => count($attrb) > 0 ? $attrb[1] : null,
                ]);
            }

            $logged = $this->orderTransactionService->logFromTemplate(
                (int) $id,
                'transaction_texts.update_order',
                [],
            );

            if (! $logged) {
                return back()->with('warning', 'Something Went Wrong');
            }

            $courierId = (int) ($request->courier_id ?? 0);
            $shippingArea = (int) ($request->shipping_area ?? 0);

            if ($courierId > 0) {
                $this->orderCourierService->applyCourierChargeCost(
                    $order_id,
                    $courierId,
                    $shippingArea,
                );
            }

            if ((int) ($request->old_status ?? 0) !== OrderStatus::PendingInvoice->value && (int) ($request->status ?? 0) === OrderStatus::PendingInvoice->value) {
                $this->orderCustomerNotificationService->sendOrderConfirmSmsIfEnabled($order_id);

                $this->orderCourierService->sendOrderToCourier($order_id);
            }

            $order_id->update([
                'status' => $request->status,
            ]);

            $this->orderCustomerNotificationService->notifyWhatsappForStatus($order_id, (int) $order_id->status);

            return $this->redirectToOrders('success', 'Order Updated Successfully');
        } else {
            return back()->with('error', 'Please Select A Product');
        }
    }

    private function redirectToOrders(string $flashKey, string $message)
    {
        $guard = match (true) {
            Auth::guard('admin')->check() => 'admin.orders',
            Auth::guard('manager')->check() => 'manager.orders',
            Auth::guard('employee')->check() => 'employee.orders',
            default => null,
        };

        return $guard ? to_route($guard)->with($flashKey, $message) : back()->with('warning', 'Something Went Wrong');
    }

    public function statusChange($id, $status)
    {
        $order_id = Order::with('get_products.get_product')->find($id);

        if (! $order_id) {
            return back()->with('error', 'Order Not Found');
        }

        $sms = SmsSetting::where('status', $status)->first();
        $this->orderCustomerNotificationService->notifyForStatusChange($order_id, (int) $status, $sms);

        $order_id->update([
            'status' => $status,
        ]);

        $status_name = OrderStatus::tryFrom((int) $status)?->label() ?? '';

        $logged = $this->orderTransactionService->logFromTemplate(
            (int) $id,
            'transaction_texts.order_status_change',
            ['{status}' => $status_name],
        );

        if (! $logged) {
            return back()->with('warning', 'Something Went Wrong');
        }

        return back()->with('success', 'Order Status Changed Successfully');
    }

    public function paymentStatusChange($id, $status)
    {
        $order = Order::find($id);

        if (! $order) {
            return back()->with('error', 'Order Not Found');
        }

        $order->update([
            'payment_status' => $status,
        ]);

        // create transaction
        $status_name = PaymentStatus::tryFrom((int) $status)?->label() ?? '';

        $logged = $this->orderTransactionService->logFromTemplate(
            (int) $id,
            'transaction_texts.order_payment_status_change',
            ['{status}' => $status_name],
        );

        if (! $logged) {
            return back()->with('warning', 'Something Went Wrong');
        }

        return back()->with('success', 'Order Payment Status Changed Successfully');
    }

    public function delete($id)
    {
        $order = Order::find($id);

        if (! $order) {
            return back()->with('error', 'Order Not Found');
        }

        if ($order->status == 1) {
            return back()->with('warning', 'Completed Order Can\'t Be Deleted!');
        } else {
            $order->delete();

            return back()->with('success', 'Order Deleted Successfully');
        }
    }

    private function actorIdString(): ?string
    {
        return match (true) {
            Auth::guard('admin')->check() => 'admin-'.Auth::guard('admin')->id(),
            Auth::guard('manager')->check() => 'manager-'.Auth::guard('manager')->id(),
            Auth::guard('employee')->check() => 'employee-'.Auth::guard('employee')->id(),
            default => null,
        };
    }

    public function ajaxGetProducts(Request $request)
    {
        return view('backEnd.admin.orders.products', [
            'data' => Product::with('get_thumb')->find($request->id),
        ])->render();
    }

    public function printInvoice(Request $request)
    {
        return view('backEnd.admin.orders.invoice2', [
            'data' => Order::find($request->id),
        ])->render();
    }

    public function printBulkInvoice(Request $request)
    {
        return view('backEnd.admin.orders.bulk_invoice_2', [
            'data' => Order::find($request->all_inv_id),
        ])->render();
    }

    public function printBulkLabelInvoice(Request $request)
    {
        $d = Order::join('order_products', 'order_products.order_id', 'orders.id')
            ->join('products', 'products.id', 'order_products.product_id')
            ->orderBy('products.name', 'asc')
            ->whereIn('orders.id', $request->all_inv_id)->pluck('orders.id')->toArray();
        $ids_ordered = implode(',', $d);
        $data = Order::orderByRaw("FIELD(id, $ids_ordered)")
            ->find($d);

        return view('backEnd.admin.orders.bulk_label_invoice', compact('data'))->render();
    }

    public function allStatusChange(Request $request)
    {
        $orderIds = $this->orderAssignmentService->parseCommaSeparatedIds($request->all_status);
        if (count($orderIds) === 0) {
            return back()->with('warning', 'No orders selected');
        }

        $sms = SmsSetting::where('status', $request->status)->first();
        $status_name = OrderStatus::tryFrom((int) $request->status)?->label() ?? '';

        foreach ($orderIds as $item) {
            $order_id = Order::with('get_products.get_product')->find($item);
            if (! $order_id) {
                continue;
            }

            $this->orderCustomerNotificationService->notifyForStatusChange($order_id, (int) $request->status, $sms);

            if ($order_id->status != 5 && $request->status == 5) {
                // Send to courier when status changes to 5 (Ready to Ship)
                if ($order_id->courier_id == 2) {
                    $this->orderCourierService->sendToRedxSingle($order_id);
                } elseif ($order_id->courier_id == 3) {
                    $this->orderCourierService->sendToSteadfastSingle($order_id);
                }

                $order_id->update(['status' => $request->status]);
            } else {
                $order_id->update(['status' => $request->status]);
            }

            // create transaction
            $this->orderTransactionService->logFromTemplate(
                (int) $item,
                'transaction_texts.order_status_change',
                ['{status}' => $status_name],
            );
        }

        return back()->with('success', 'Order Status Changed Successfully');
    }

    public function bulkDelete(Request $request)
    {
        $orderIds = $this->orderAssignmentService->parseCommaSeparatedIds($request->all_id);
        if (count($orderIds) === 0) {
            return back()->with('warning', 'No orders selected');
        }

        Order::whereIn('id', $orderIds)->delete();

        return back()->with('success', 'Deleted Successfully');
    }

    public function bulkAssign(Request $request)
    {
        $orderIds = $this->orderAssignmentService->parseCommaSeparatedIds($request->all_order_id);
        $employeeId = (int) $request->employee_id;

        if (count($orderIds) === 0) {
            return back()->with('warning', 'No orders selected');
        }

        if ($employeeId <= 0) {
            return back()->with('error', 'Please select an employee');
        }

        $employee = Employee::query()->select('id', 'name')->find($employeeId);
        if (! $employee) {
            return back()->with('error', 'Employee Not Found');
        }

        [$user, $created_by] = $this->actingUserContextResolver->resolve();
        if (! $user || ! $created_by) {
            return back()->with('warning', 'Something Went Wrong');
        }

        DB::transaction(function () use ($orderIds, $employeeId, $employee, $user, $created_by): void {
            $this->orderAssignmentService->assignOrdersToEmployee($orderIds, $employeeId);

            foreach ($orderIds as $orderId) {
                $this->orderTransactionService->logFromTemplateForActor(
                    (int) $orderId,
                    'transaction_texts.order_assign',
                    ['{employee_name}' => $employee->name],
                    $created_by,
                    (int) $user->id,
                    (string) $user->name,
                    $employeeId,
                );
            }
        });

        return back()->with('success', 'Assigned Successfully');
    }

    public function bulkEqualAssign(Request $request)
    {
        $active_employees = Employee::where('status', 1)->where('start_time', '<=', \Illuminate\Support\Facades\Date::now()->toTimeString())->where('end_time', '>=', \Illuminate\Support\Facades\Date::now()->toTimeString())->get();

        if ($active_employees->count() === 0) {
            return back()->with('error', 'No active employees found');
        }

        $orderIds = $this->orderAssignmentService->parseCommaSeparatedIds($request->eq_assign_order_ids);
        if (count($orderIds) === 0) {
            return back()->with('warning', 'No orders selected');
        }

        $total_orders = Order::query()->whereIn('id', $orderIds)->select('id')->get();
        if ($total_orders->count() === 0) {
            return back()->with('warning', 'No valid orders selected');
        }

        $per_emp_orders = (int) ceil($total_orders->count() / $active_employees->count());

        $skip = 0;
        [$user, $created_by] = $this->actingUserContextResolver->resolve();
        if (! $user || ! $created_by) {
            return back()->with('warning', 'Something Went Wrong');
        }

        DB::transaction(function () use ($active_employees, $total_orders, $per_emp_orders, $user, $created_by, &$skip): void {
            foreach ($active_employees as $active_employee) {
                $chunk = $total_orders->skip($skip)->take($per_emp_orders);
                $chunkIds = $chunk->pluck('id')->map(fn ($id) => (int) $id)->all();

                if (count($chunkIds) === 0) {
                    break;
                }

                $this->orderAssignmentService->assignOrdersToEmployee($chunkIds, (int) $active_employee->id);

                foreach ($chunkIds as $orderId) {
                    $this->orderTransactionService->logFromTemplateForActor(
                        (int) $orderId,
                        'transaction_texts.order_assign',
                        ['{employee_name}' => $active_employee->name],
                        $created_by,
                        (int) $user->id,
                        (string) $user->name,
                        (int) $active_employee->id,
                    );
                }

                $skip += $per_emp_orders;
            }
        });

        return back()->with('success', 'Equal Assign Completed');
    }

    public function singleAssign(Request $request)
    {
        $orderId = (int) $request->order_id;
        $employeeId = (int) $request->employee_id;

        if ($orderId <= 0) {
            return back()->with('error', 'Invalid order');
        }

        if ($employeeId <= 0) {
            return back()->with('error', 'Please select an employee');
        }

        $employee = Employee::query()->select('id', 'name')->find($employeeId);
        if (! $employee) {
            return back()->with('error', 'Employee Not Found');
        }

        [$user, $created_by] = $this->actingUserContextResolver->resolve();
        if (! $user || ! $created_by) {
            return back()->with('warning', 'Something Went Wrong');
        }

        DB::transaction(function () use ($orderId, $employeeId, $employee, $user, $created_by): void {
            $this->orderAssignmentService->assignOrdersToEmployee([$orderId], $employeeId);

            $this->orderTransactionService->logFromTemplateForActor(
                (int) $orderId,
                'transaction_texts.order_assign',
                ['{employee_name}' => $employee->name],
                $created_by,
                (int) $user->id,
                (string) $user->name,
                $employeeId,
            );
        });

        return back()->with('success', 'Assigned Successfully');
    }

    public function courierCsv(Request $request)
    {
        $config = match ((int) $request->courier_csv) {
            1 => ['name' => 'pathao', 'ext' => 'csv'],
            2 => ['name' => 'redex', 'ext' => 'xlsx'],
            3 => ['name' => 'paperfly', 'ext' => 'xlsx'],
            4 => ['name' => 'stead_fast', 'ext' => 'xlsx'],
            0 => ['name' => 'export_orders', 'ext' => 'xlsx'],
            default => null,
        };

        if (! $config) {
            return back()->with('error', 'Something Went Wrong');
        }

        return Excel::download(
            new OrderExport(explode(',', $request->all_ord_id), (int) $request->courier_csv),
            $config['name'].'_'.date('d-M-Y').'.'.$config['ext']
        );
    }

    public function transactionView(Request $request)
    {
        return view('backEnd.admin.orders.transactions', [
            'transactions' => OrderTransaction::select('type', 'text', 'created_at')
                ->where('order_id', $request->id)
                ->orderBy('id', 'desc')
                ->get(),
        ])->render();
    }

    public function noteUpdate(Request $request)
    {
        $logged = $this->orderNoteService->addNoteHistory(
            (int) $request->id,
            (string) ($request->courier_note ?? $request->staff_note)
        );

        if (! $logged) {
            return back()->with('warning', 'Something Went Wrong');
        }

        Order::find($request->id)->update(
            $request->has('courier_note')
                ? ['courier_note' => $request->courier_note]
                : ['staff_note' => $request->staff_note]
        );

        return back()->with('success', 'Note Updated Successfully');
    }

    public function steadFastOrderSync()
    {
        $result = $this->orderCourierService->syncSteadfastStatuses();

        return back()->with($result['status'], $result['message']);
    }

    public function sendToCourier(Request $request)
    {
        if (! $ids = $this->orderAssignmentService->parseCommaSeparatedIds($request->all_status)) {
            return back()->with('warning', 'No orders selected');
        }

        return back()->with(match ((int) $request->send_to_courier) {
            1 => (array) $this->orderCourierService->sendToPathao($ids),
            4 => (array) $this->orderCourierService->sendToCarrybee($ids),
            default => ['status' => 'error', 'message' => 'Something Went Wrong'],
        });
    }

    public function getShipping(Request $request)
    {
        return response()->json(ShippingMethod::where('id', $request->id)->first());
    }

    public function retryForwarding(int $id)
    {
        $order = Order::with('get_products.get_product')->find($id);

        if (! $order) {
            return back()->with('error', 'Order Not Found');
        }

        $this->orderForwardingService->retryForwarding($order);

        return back()->with('success', 'Forwarding retriggered for this order');
    }
}
