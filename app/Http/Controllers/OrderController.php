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
use App\Services\WhatsappServices;
use Darryldecode\Cart\Facades\CartFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    public function __construct(protected WhatsappServices $WpServices) {}

    public function checkout()
    {
        visitor()->visit();
        $cart = CartFacade::getContent();

        if (count($cart) > 0) {
            $this->storeCheckoutConversionData($cart);
        }

        $shipping_methods = ShippingMethod::where('status', 1)->get();

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

        $carts = CartFacade::getContent();
        if ($carts->count() == 0) {
            return to_route('home')->with('error', 'Please Select Products');
        }

        $invoice_id = $this->generateInvoiceId();
        $customer_id = $this->getOrCreateCustomer($request);

        if (! $customer_id) {
            CartFacade::clear();

            return to_route('home')->with('success', 'Order Placed Successfully');
        }

        $order_id = $this->createOrder($request, $invoice_id, $customer_id, $ip, $carts);
        $this->sendWhatsappNotification($order_id);
        $employee_id = $this->addOrderProducts($carts, $order_id);
        $this->assignEmployee($carts, $order_id, $employee_id);
        $this->handleFakeChecker($order_id);
        $this->storeOrderConversionData($order_id);
        $this->clearAbandonedCart();

        CartFacade::clear();

        $order_info = [
            'name' => $request->customer_name,
            'order_id' => $order_id->invoice_id,
            'total' => $order_id->total,
        ];

        $emp = DB::table('employees')->where('id', $employee_id)->select('name')->first();

        order_transaction(
            'local',
            $order_id->id,
            strtr(config('transaction_texts.new_order'), [
                '{user_name}' => $request->customer_name,
                '{role}' => 'customer',
                '{employee_name}' => $emp->name ?? 'N/A',
            ]),
            null,
            'customer',
            $customer_id->id,
            $employee_id
        );

        return to_route('confirm.order')->with('success', 'Order Placed Successfully')->with('order_info', $order_info);
    }

    public function confirm()
    {
        $settings = DB::table('web_settings')->where('id', 1)->first();

        if ($settings->fb_pixel_id) {
            $this->sendFacebookConversionEvent($settings);
        }

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
        if (! empty(\Illuminate\Support\Facades\Request::server('HTTP_CLIENT_IP'))) {
            $ip = \Illuminate\Support\Facades\Request::server('HTTP_CLIENT_IP');
        } elseif (! empty(\Illuminate\Support\Facades\Request::server('HTTP_X_FORWARDED_FOR'))) {
            $ip = \Illuminate\Support\Facades\Request::server('HTTP_X_FORWARDED_FOR');
        } else {
            $ip = \Illuminate\Support\Facades\Request::server('REMOTE_ADDR');
        }

        return $ip == '::1' ? gethostname() : $ip;
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
        return Order::create(array_merge($request->all(), [
            'invoice_id' => $invoice_id,
            'order_date' => \Illuminate\Support\Facades\Date::now()->toDateString(),
            'customer_id' => $customer_id->id,
            'sub_total' => CartFacade::getSubTotal(),
            'total' => (CartFacade::getTotal() + $request->shipping_cost),
            'due' => (CartFacade::getTotal() + $request->shipping_cost),
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
        $p_id = null;

        foreach ($carts as $item) {
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $item->id,
                'qty' => $item->quantity,
                'price' => $item->price,
                'purchase_cost' => $item->associatedModel->purchase_cost,
                'attributes' => $item->attributes->count() > 0 ? $item->attributes[0] : null,
                'attribute_ids' => $item->attributes->count() > 0 ? $item->attributes[1] : null,
            ]);
            $p_id = $item->id;
        }

        return $p_id;
    }

    private function assignEmployee($carts, Order $order, ?int $product_id): ?int
    {
        if ($carts->count() == 1 && $product_id) {
            $employee_id = UserProducts::join('employees', 'employees.id', 'user_products.user_id')
                ->where('user_products.product_id', $product_id)
                ->where('employees.status', 1)
                ->get();

            if ($employee_id->count() > 0) {
                $employees = [];
                foreach ($employee_id as $item) {
                    $employees[$item->id] = $item->name;
                }
                $selected_id = array_rand($employees);

                OrderAssign::create(['order_id' => $order->id, 'employee_id' => $selected_id]);

                return $selected_id;
            }
        }

        return $this->assignRandomEmployee($order);
    }

    private function assignRandomEmployee(Order $order): ?int
    {
        $employees = Employee::where('status', 1)
            ->where('start_time', '<=', \Illuminate\Support\Facades\Date::now()->toTimeString())
            ->where('end_time', '>=', \Illuminate\Support\Facades\Date::now()->toTimeString())
            ->get();

        if ($employees->count() > 0) {
            $emp_list = [];
            foreach ($employees as $item) {
                $emp_list[$item->id] = $item->name;
            }
            $selected_id = array_rand($emp_list);

            OrderAssign::create(['order_id' => $order->id, 'employee_id' => $selected_id]);

            return $selected_id;
        }

        return null;
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
        $order_prod = [];
        $i = -1;

        foreach ($cart as $item) {
            $i++;
            $order_prod[$i] = [
                'index' => $i,
                'item_id' => $item->associatedModel->id,
                'item_name' => $item->name,
                'item_category' => count($item->associatedModel->get_categories) > 0 ? $item->associatedModel->get_categories[0]->category_name : '',
                'price' => $item->associatedModel->sale_price ? number_format($item->associatedModel->sale_price, 2, '.', '') : number_format($item->associatedModel->price, 2, '.', ''),
                'quantity' => $item->quantity,
            ];
        }

        session()->put('api_begin_checkout_data', [
            'value' => CartFacade::getTotal(),
            'products' => json_encode($order_prod),
        ]);
    }

    private function storeOrderConversionData(Order $order): void
    {
        $order_prod = [];

        foreach ($order->get_products as $key => $get_product) {
            $order_prod[$key] = [
                'index' => $key,
                'item_id' => $get_product->get_product->id,
                'item_category' => count($get_product->get_product->get_categories) > 0 ? $get_product->get_product->get_categories[0]->category_name : '',
                'item_name' => $get_product->get_product->name,
                'price' => $get_product->get_product->sale_price ? number_format($get_product->get_product->sale_price, 2, '.', '') : number_format($get_product->get_product->price, 2, '.', ''),
                'quantity' => $get_product->qty,
            ];
        }

        session()->put('api_purchase_data', [
            'customer_id' => $order->customer_id,
            'full_name' => $order->customer_name,
            'phone' => $order->customer_phone,
            'email' => $order->customer_email,
            'address_summary' => $order->customer_address,
            'invoice_id' => $order->invoice_id,
            'sub_total' => $order->sub_total,
            'shipping_cost' => $order->shipping_cost,
            'products' => json_encode($order_prod),
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

    private function sendFacebookConversionEvent($settings): void
    {
        $user_ip = $this->getUserIP();
        $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $data = '{
            "data": [
                {
                    "action_source": "website",
                    "event_name": "Purchase",
                    "event_time": '.time().',
                    "event_source_url": "'.$actual_link.'",
                    "user_data": {
                        "fn": ["'.hash('sha256', (string) session('order_info')['name']).'"],
                        "country": ["'.hash('sha256', 'BD').'"],
                        "ph": ["'.hash('sha256', (string) session('order_info')['phone']).'"],
                        "external_id": ["'.hash('sha256', (string) session('order_info')['user_id']).'"],
                        "client_ip_address": "'.$user_ip.'",
                        "client_user_agent": "'.\Illuminate\Support\Facades\Request::server('HTTP_USER_AGENT').'"
                    },
                    "custom_data": {
                        "currency": "BDT",
                        "value": "'.session('order_info')['total'].'"
                    }
                }
            ]
        }';

        $url = 'https://graph.facebook.com/v17.0/'.$settings->fb_pixel_id.'/events';
        $_curl_ = new ConversionAPI;
        $_curl_->post_request($url, $data);
    }

    private function getUserIP(): string
    {
        if (! empty(\Illuminate\Support\Facades\Request::server('HTTP_CLIENT_IP'))) {
            return \Illuminate\Support\Facades\Request::server('HTTP_CLIENT_IP');
        }

        if (! empty(\Illuminate\Support\Facades\Request::server('HTTP_X_FORWARDED_FOR'))) {
            return \Illuminate\Support\Facades\Request::server('HTTP_X_FORWARDED_FOR');
        }

        return \Illuminate\Support\Facades\Request::server('REMOTE_ADDR');
    }
}
