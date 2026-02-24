<?php

namespace App\Http\Controllers;

use App\Models\AbandonedCart;
use App\Models\Employee;
use App\Models\IP;
use App\Models\Order;
use App\Models\OrderAssign;
use App\Models\OrderProduct;
use App\Models\ShippingMethod;
use App\Models\SmsSetting;
use App\Models\User;
use App\Models\UserProducts;
use App\Services\ConversionAPI;
use App\Services\OrderDefenderService;
use App\Services\WhatsappServices;
use Darryldecode\Cart\Facades\CartFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request as RequestFacade;

class OrderController extends Controller
{
    public function __construct(
        protected WhatsappServices $WpServices,
        protected ConversionAPI $conversionAPI,
        protected OrderDefenderService $orderDefender,
    ) {}

    public function checkout()
    {
        visitor()->visit();
        $cart = CartFacade::getContent();

        if (count($cart) > 0) {
            $this->storeCheckoutConversionData($cart);
        }

        $shipping_methods = ShippingMethod::where('status', 1)->get();

        $this->conversionAPI->trackEvent('InitiateCheckout', [
            'currency' => 'BDT',
            'value' => CartFacade::getTotal(),
            'num_items' => CartFacade::getContent()->count(),
            'page_url' => url()->current(),
            'content_ids' => CartFacade::getContent()->pluck('id')->toArray(),
            'content_name' => CartFacade::getContent()->pluck('name')->implode('; '),
        ]);

        return view('frontEnd.checkout', compact('shipping_methods'));
    }

    public function place(Request $request)
    {
        $request->validate([
            'customer_phone' => ['required', 'min:11', 'max:11'],
        ], [
            'customer_phone.required' => 'অনুগ্রহ করে আপনার মোবাইল নাম্বারটি দিন',
            'customer_phone.min' => 'আপনার মোবাইল নাম্বারটি সঠিক নয়',
            'customer_phone.max' => 'আপনার মোবাইল নাম্বারটি সঠিক নয়',
        ]);

        $ip = $this->getClientIP();
        if (! $this->checkIPAllowed($ip)) {
            return to_route('home')->with('success', 'Order Placed Successfully');
        }

        // Order Defender: pre-order rate limit check
        $defenderResult = $this->orderDefender->check($ip, $request->customer_phone);
        if (! $defenderResult['allowed']) {
            return to_route('home')->with('success', 'Order Placed Successfully');
        }

        $carts = CartFacade::getContent();
        if ($carts->isEmpty()) {
            return to_route('home')->with('error', 'Please Select Products');
        }

        $invoice_id = $this->generateInvoiceId();
        $customer = $this->getOrCreateCustomer($request);

        if (! $customer) {
            CartFacade::clear();

            return to_route('home')->with('success', 'Order Placed Successfully');
        }

        $order = $this->createOrder($request, $invoice_id, $customer, $ip, $carts);
        $this->sendWhatsappNotification($order);
        $last_product_id = $this->addOrderProducts($carts, $order);
        $employee_id = $this->assignEmployee($carts, $order, $last_product_id);
        $this->handleFakeChecker($order);
        $this->orderDefender->flagOrderIfNeeded($order, $ip, $request->customer_phone);
        $this->storeOrderConversionData($order);
        $this->clearAbandonedCart();
        $this->createOrderTransaction($request, $order, $customer->id, $employee_id);

        CartFacade::clear();

        $order_info = [
            'name' => $request->customer_name,
            'order_id' => $order->invoice_id,
            'total' => $order->total,
        ];

        $this->conversionAPI->trackPurchase([
            'id' => $order->id,
            'total' => $order->total,
        ], $carts->map(function ($item) {
            return [
                'id' => $item->id,
                'quantity' => $item->quantity,
            ];
        })->toArray(), [
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'external_id' => $customer->id,
        ]);

        return to_route('confirm.order')->with('success', 'Order Placed Successfully')->with('order_info', $order_info);
    }

    public function confirm()
    {
        return view('frontEnd.order_confirmed');
    }

    public function track(Request $request)
    {
        visitor()->visit();
        $query = $request->q;

        $data = $query
            ? Order::with('get_products')->where('customer_phone', $query)->orderBy('invoice_id', 'desc')->get()
            : [];

        return view('frontEnd.track_order', compact('data'));
    }

    private function getClientIP(): string
    {
        $ip = RequestFacade::server('HTTP_CLIENT_IP')
            ?? RequestFacade::server('HTTP_X_FORWARDED_FOR')
            ?? RequestFacade::server('REMOTE_ADDR');

        return $ip === '::1' ? gethostname() : $ip;
    }

    private function checkIPAllowed(string $ip): bool
    {
        $check_ip = DB::table('i_p_s')->where('ip_address', $ip)->first();

        if ($check_ip && $check_ip->status == 1) {
            return false;
        }

        if (! $check_ip) {
            IP::create(['ip_address' => $ip]);
        }

        return true;
    }

    private function generateInvoiceId(): string
    {
        if (Order::withTrashed()->count() > 0) {
            $invoice_id = Order::withTrashed()->latest('id')->first()->invoice_id;
            $invoice_id = trim((string) $invoice_id, 'INV');
            $invoice_id++;

            return 'INV'.$invoice_id;
        }

        return 'INV1';
    }

    private function getOrCreateCustomer(Request $request): ?User
    {
        $check_cus = User::where('phone', $request->customer_phone)->first();

        if ($check_cus) {
            return $check_cus->status == 1 ? $check_cus : null;
        }

        return User::create([
            'name' => $request->customer_name,
            'phone' => $request->customer_phone,
            'address' => $request->customer_address,
            'password' => Hash::make($request->customer_phone),
        ]);
    }

    private function createOrder(Request $request, string $invoice_id, User $customer_id, string $ip, $carts): Order
    {
        $cartTotal = CartFacade::getTotal();
        $shippingCost = (float) $request->shipping_cost;
        $extraDiscount = (float) $request->input('extra_discount', 0);

        $total = $cartTotal + $shippingCost - $extraDiscount;
        if ($total < 0) {
            $total = 0;
        }

        return Order::create(array_merge($request->all(), [
            'invoice_id' => $invoice_id,
            'order_date' => Date::now()->toDateString(),
            'customer_id' => $customer_id->id,
            'sub_total' => CartFacade::getSubTotal(),
            'discount' => $extraDiscount,
            'total' => $total,
            'due' => $total,
            'status' => 2,
            'ip_address' => $ip,
            'source' => 'direct',
        ]));
    }

    private function sendWhatsappNotification(Order $order): void
    {
        $sms = SmsSetting::where('status', $order->status)->first();

        if ($sms && $sms->is_whatsapp == 1 && $sms->template_name != null) {
            $this->WpServices->sendOrderWhatsapp($order, $sms->template_name, $sms->status);
        }
    }

    private function addOrderProducts($carts, Order $order): ?int
    {
        $lastProductId = null;

        foreach ($carts as $item) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $item->id,
                'qty' => $item->quantity,
                'price' => $item->price,
                'purchase_cost' => $item->associatedModel->purchase_cost,
                'attributes' => $item->attributes->isNotEmpty() ? $item->attributes[0] : null,
                'attribute_ids' => $item->attributes->isNotEmpty() ? $item->attributes[1] : null,
            ]);
            $lastProductId = $item->id;
        }

        return $lastProductId;
    }

    private function assignEmployee($carts, Order $order, ?int $product_id): ?int
    {
        if ($carts->count() === 1 && $product_id) {
            $productEmployees = UserProducts::join('employees', 'employees.id', 'user_products.user_id')
                ->where('user_products.product_id', $product_id)
                ->where('employees.status', 1)
                ->pluck('employees.name', 'employees.id');

            if ($productEmployees->isNotEmpty()) {
                return $this->assignRandomEmployeeFromList($order, $productEmployees->toArray());
            }
        }

        return $this->assignRandomEmployee($order);
    }

    private function assignRandomEmployee(Order $order): ?int
    {
        $currentTime = Date::now()->toTimeString();
        $employees = Employee::where('status', 1)
            ->where('start_time', '<=', $currentTime)
            ->where('end_time', '>=', $currentTime)
            ->pluck('name', 'id');

        return $employees->isNotEmpty()
            ? $this->assignRandomEmployeeFromList($order, $employees->toArray())
            : null;
    }

    private function handleFakeChecker(Order $order): void
    {
        if (session()->has('fake_checker')) {
            DB::table('orders')->where('id', $order->id)->update(['is_fake' => 1]);

            $chck_fake = Order::where('id', session()->get('fake_checker'))->first();
            if ($chck_fake && $chck_fake->is_fake == 0) {
                $chck_fake->update(['is_fake' => 1]);
            }
        } else {
            session()->put('fake_checker', $order->id);
        }
    }

    private function storeCheckoutConversionData($cart): void
    {
        $orderProducts = $cart->map(fn ($item, $index) => [
            'index' => $index,
            'item_id' => $item->associatedModel->id,
            'item_name' => $item->name,
            'item_category' => $this->getCategoryName($item->associatedModel),
            'price' => $this->formatPrice($this->getProductPrice($item->associatedModel)),
            'quantity' => $item->quantity,
        ]);

        session()->put('api_begin_checkout_data', [
            'value' => CartFacade::getTotal(),
            'products' => json_encode($orderProducts->values()),
        ]);
    }

    private function storeOrderConversionData(Order $order): void
    {
        $orderProducts = $order->get_products->map(fn ($item, $index) => [
            'index' => $index,
            'item_id' => $item->get_product->id,
            'item_category' => $this->getCategoryName($item->get_product),
            'item_name' => $item->get_product->name,
            'price' => $this->formatPrice($this->getProductPrice($item->get_product)),
            'quantity' => $item->qty,
        ]);

        session()->put('api_purchase_data', [
            'customer_id' => $order->customer_id,
            'full_name' => $order->customer_name,
            'phone' => $order->customer_phone,
            'email' => $order->customer_email,
            'address_summary' => $order->customer_address,
            'invoice_id' => $order->invoice_id,
            'sub_total' => $order->sub_total,
            'shipping_cost' => $order->shipping_cost,
            'products' => json_encode($orderProducts->values()),
        ]);
    }

    private function clearAbandonedCart(): void
    {
        if (session()->has('abandoned_cart_id')) {
            $abandoned = AbandonedCart::where('id', session()->get('abandoned_cart_id'))->first();
            $abandoned?->delete();
            session()->forget('abandoned_cart_id');
        }
    }

    private function assignRandomEmployeeFromList(Order $order, array $employees): int
    {
        $selectedId = array_rand($employees);
        OrderAssign::create(['order_id' => $order->id, 'employee_id' => $selectedId]);

        return $selectedId;
    }

    private function createOrderTransaction(Request $request, Order $order, int $customerId, ?int $employeeId): void
    {
        $employeeName = $employeeId
            ? DB::table('employees')->where('id', $employeeId)->value('name') ?? 'N/A'
            : 'N/A';

        order_transaction(
            'local',
            $order->id,
            strtr(config('transaction_texts.new_order'), [
                '{user_name}' => $request->customer_name,
                '{role}' => 'customer',
                '{employee_name}' => $employeeName,
            ]),
            null,
            'customer',
            $customerId,
            $employeeId
        );
    }

    private function getProductPrice($product): float
    {
        return $product->sale_price > 0 ? $product->sale_price : $product->price;
    }

    private function formatPrice(float $price): string
    {
        return number_format($price, 2, '.', '');
    }

    private function getCategoryName($product): string
    {
        return $product->get_categories[0]->category_name ?? '';
    }
}
