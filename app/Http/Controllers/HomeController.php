<?php

namespace App\Http\Controllers;

use App\IP;
use App\User;
use App\Order;
use App\Slider;
use App\Product;
use App\Category;
use App\Employee;
use App\Attribute;
use Carbon\Carbon;
use App\SmsSetting;
use App\OrderAssign;
use App\WebSettings;
use App\OrderProduct;
use App\UserProducts;
use App\AbandonedCart;
use App\AttributeItem;
use App\ConversionAPI;
use App\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Http\Services\WhatsappServices;

class HomeController extends Controller
{
    protected $WpServices;

    public function __construct(WhatsappServices $WpServices)
    {
        $this->WpServices = $WpServices;
    }

    public function index()
    {
        visitor()->visit();
        /*$a = OrderProduct::select('product_id')
        ->groupBy('product_id')
        ->pluck('product_id');
        $b = [];
        foreach ($a as $key => $item) {
        $prod = Product::find($item)->name;
        $b += [
        $item => $prod
        ];
        }
        dd($b);*/

        //dd(json_decode('{"consignment_id":"DL121224VS8TTJ","merchant_order_id":null,"updated_at":"2025-02-06 04:33:02","timestamp":"2025-02-05T22:33:02+00:00","store_id":122860,"event":"order.created","delivery_fee":83.46,"is_sample":true,"order_status":"Order Created","order_status_slug":"Order_Created"}', true));
        $categories = Category::where('status', 1)->get();
        $products = Product::with('get_thumb')->where('status', 1)->orderBy('id', 'desc')->paginate(30);
        $hot_deal_1 = Product::with('get_thumb')->where([['sale_price', '>', 0], ['status', 1]])->take(12)->orderBy('id', 'desc')->get();
        $hot_deal_2 = Product::with('get_thumb')->where([['sale_price', '>', 0], ['status', 1]])->skip(12)->take(12)->orderBy('id', 'desc')->get();
        $sliders = Slider::with('get_img')->where('status', 1)->get();
        $categoryProducts = Category::with('get_products')->where('parent', null)->get();
        $best_selling = OrderProduct::with('get_product')->select('product_id', DB::raw('count(*) as total'))
            ->whereHas('get_order', function ($query): void {
                $query->where('created_at', '>=', \Illuminate\Support\Facades\Date::now()->subDays(7));
            })
            ->groupBy('product_id')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();
        return view('frontEnd.index', compact('products', 'hot_deal_1', 'hot_deal_2', 'categories', 'sliders', 'categoryProducts', 'best_selling'));
    }

    public function aboutUs()
    {
        visitor()->visit();
        $data = DB::table('page_settings')->where('id', 1)->first();
        return view('frontEnd.pages.about_us', compact('data'));
    }

    public function returnPolicy()
    {
        visitor()->visit();
        $data = DB::table('page_settings')->where('id', 1)->first();
        return view('frontEnd.pages.return_policy', compact('data'));
    }

    public function deliveryPolicy()
    {
        visitor()->visit();
        $data = DB::table('page_settings')->where('id', 1)->first();
        return view('frontEnd.pages.delivery_policy', compact('data'));
    }

    public function getSingleCategory($id)
    {
        visitor()->visit();
        /*$data = Product::with('get_thumb')->where('category_id', $id)->paginate(42);
        $cat_name = Category::find($id)->category_name;*/
        $category = Category::with('get_products')->find($id);
        $data = $category->get_products()->with('get_thumb')->paginate(42);
        $cat_name = $category->category_name;
        return view('frontEnd.single_category', compact('data', 'cat_name'));
    }

    public function getSingleProduct($slug, $id)
    {
        visitor()->visit();
        $data = Product::with('get_image', 'get_category', 'get_categories')->where([['id', $id], ['status', 1]])->first();
        //dd($data);
        abort_unless($data, 404);
        $feature_prod = Product::with('get_thumb')->where('status', 1)->orderBy('id', 'desc')->take(3)->get();

        $category = Category::with('get_products')->find($data->get_category->category_id);
        if ($category) {
            $related_prod = $category->get_products()->with('get_thumb')->inRandomOrder()->take(12)->get();
        } else {
            $related_prod = [];
        }

        //for conversion api
        $order_prod[] = [
            'item_id' => $data->id,
            'item_name' => $data->name,
            'item_category' => count($data->get_categories) > 0 ? $data->get_categories[0]->category_name : "",
            'price' => $data->sale_price ? number_format($data->sale_price, 2, '.', '') : number_format($data->price, 2, '.', ''),
            'quantity' => 1,
        ];

        $api_data = [
            'value' => $data->sale_price ? number_format($data->sale_price, 2, '.', '') : number_format($data->price, 2, '.', ''),
            'products' => json_encode($order_prod),
        ];

        session()->put('api_view_item_data', $api_data);

        $shipping_methods = DB::table('shipping_methods')->where('status', 1)->get();
        $qty = \Cart::get($id)->quantity ?? 1;
        //dd($qty);
        return view('frontEnd.single_product', compact('data', 'related_prod', 'feature_prod', 'shipping_methods', 'qty'));
    }

    public function allHotDeals()
    {
        $data = Product::with('get_thumb')->where([['sale_price', '>', 0], ['status', 1]])->paginate(42);
        return view('frontEnd.all_hot_deals', compact('data'));
    }

    public function addCart(Request $request, $id)
    {
        //dd($request->all());
        if ($request->attribute_id) {
            foreach ($request->attribute_id as $item) {
                $an = Attribute::find($item)->title;
                $ain = AttributeItem::find($request->attribute_item_id[$item][0])->item_title;
                $attr[0][$an] = $ain;
            }
            foreach ($request->attribute_id as $item) {
                $an = $item;
                $ain = $request->attribute_item_id[$item][0];
                $attr[1][$an] = $ain;
            }
            $attrb[0] = json_encode($attr[0]);
            $attrb[1] = json_encode($attr[1]);
        } else {
            $attrb = null;
        }

        //dd($attr);
        $quantity = $request->qty ?? 1;
        $product = Product::with('get_categories')->find($id);

        if ($product->sale_price > 0) {
            $price = $product->sale_price;
        } else {
            $price = $product->price;
        }

        if (\Cart::get($id)) {
            \Cart::update($id, [
                'quantity' => [
                    'relative' => false,
                    'value' => $quantity,
                ],
                'attributes' => $attrb,
            ]);
        } else {
            \Cart::add([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'attributes' => $attrb,
                'associatedModel' => $product,
            ]);
        }

        if ($request->order_now) {
            return to_route('checkout')->with('success', 'Product Added Into Cart Successfully');
        } elseif ($request->add_cart) {
            //for conversion api
            $order_prod[] = [
                'item_id' => $product->id,
                'item_name' => $product->name,
                'item_category' => count($product->get_categories) > 0 ? $product->get_categories[0]->category_name : "",
                'price' => $product->sale_price ? number_format($product->sale_price, 2, '.', '') : number_format($product->price, 2, '.', ''),
                'quantity' => $quantity,
            ];

            $api_data = [
                'value' => $product->sale_price ? number_format($product->sale_price, 2, '.', '') : number_format($product->price, 2, '.', ''),
                'products' => json_encode($order_prod),
            ];

            session()->put('api_add_to_cart_data', $api_data);

            return back()->with(['success' => 'Product Added Into Cart Successfully', 'api_add_to_cart' => 'yes']);
        } else {
            return back()->with('error', 'Something Went Wrong!');
        }
    }

    public function cartItemDelete($id)
    {
        \Cart::remove($id);
        return back();
    }

    public function cartItemPlus(Request $request)
    {
        //dd($request->all());
        if (\Cart::getContent()->count() > 0) {
            \Cart::update($request->id, [
                'quantity' => 1,
            ]);
            return view('frontEnd.order_info_table')->render();
        } else {
            return back();
        }
    }

    public function cartItemMinus(Request $request)
    {
        if (\Cart::getContent()->count() > 0) {
            \Cart::update($request->id, [
                'quantity' => -1,
            ]);
            return view('frontEnd.order_info_table')->render();
        } else {
            return back();
        }
    }

    public function cartClear()
    {
        \Cart::clear();
        return back();
    }

    public function getShippMeth(Request $request)
    {
        if (count(\Cart::getContent()) <= 1) {
            foreach (\Cart::getContent() as $it) {
                if ($it['associatedModel']['start_date'] && $it['associatedModel']['end_date']) {
                    $amount = 0;
                } else {
                    $amount = ShippingMethod::find($request->id)->amount;
                }
            }
        } else {
            $amount = ShippingMethod::find($request->id)->amount;
        }
        return response()->json($amount);
    }

    public function checkout()
    {
        visitor()->visit();
        $cart = \Cart::getContent();
        //for conversion api
        if (count($cart) > 0) {
            $i = -1;
            foreach ($cart as $item) {
                $i++;
                $order_prod[$i] = [
                    'index' => $i,
                    'item_id' => $item->associatedModel->id,
                    'item_name' => $item->name,
                    'item_category' => count($item->associatedModel->get_categories) > 0 ? $item->associatedModel->get_categories[0]->category_name : "",
                    'price' => $item->associatedModel->sale_price ? number_format($item->associatedModel->sale_price, 2, '.', '') : number_format($item->associatedModel->price, 2, '.', ''),
                    'quantity' => $item->quantity,
                ];
            }

            $api_data = [
                'value' => \Cart::getTotal(),
                'products' => json_encode($order_prod),
            ];
            session()->put('api_begin_checkout_data', $api_data);
        }

        $shipping_methods = ShippingMethod::where('status', 1)->get();
        // dd($cart);
        return view('frontEnd.checkout', compact('shipping_methods'));
    }

    public function placeOrder(Request $request)
    {
        /**---------------------------------------
         * Validation
         * ---------------------------------------
         */
        $request->validate([
            'customer_phone' => ['required', 'min:11', 'max:11'],
        ], [
            'customer_phone.required' => 'অনুগ্রহ করে আপনার মোবাইল নাম্বারটি দিন',
            'customer_phone.min' => 'আপনার মোবাইল নাম্বারটি সঠিক নয়',
            'customer_phone.max' => 'আপনার মোবাইল নাম্বারটি সঠিক নয়',
        ]);

        /**---------------------------------------
         * Get IP Address
         * ---------------------------------------
         */
        function getIPAddress()
        {
            if (!empty(\Illuminate\Support\Facades\Request::server('HTTP_CLIENT_IP'))) {
                $ip = \Illuminate\Support\Facades\Request::server('HTTP_CLIENT_IP');
            } elseif (!empty(\Illuminate\Support\Facades\Request::server('HTTP_X_FORWARDED_FOR'))) {
                $ip = \Illuminate\Support\Facades\Request::server('HTTP_X_FORWARDED_FOR');
            } else {
                $ip = \Illuminate\Support\Facades\Request::server('REMOTE_ADDR');
            }
            return $ip;
        }

        $ip = getIPAddress();
        if ($ip == '::1') {
            $ip = gethostname();
        }
        $check_ip = DB::table('i_p_s')->where('ip_address', $ip)->first();
        if ($check_ip) {
            if ($check_ip->status == 1) {
                return to_route('home')->with('success', 'Order Placed Successfully');
            }
        } else {
            //store IP address
            IP::create([
                'ip_address' => $ip,
            ]);
        }
        /**---------------------------------------
         * Cart Add Data
         * ---------------------------------------
         */
        $carts = \Cart::getContent();
        //dd($carts);
        if ($carts->count() > 0) {
            if (Order::withTrashed()->count() > 0) {
                $invoice_id = Order::withTrashed()->latest('id')->first()->invoice_id;
                $invoice_id = trim($invoice_id, 'INV');
                $invoice_id++;
                $invoice_id = 'INV' . $invoice_id;
            } else {
                $invoice_id = 'INV1';
            }

            //create customer account
            $check_cus = User::where('phone', $request->customer_phone)->first();
            if ($check_cus) {
                if ($check_cus->status == 1) {
                    $customer_id = $check_cus;
                } else {
                    //clear the cart
                    \Cart::clear();
                    return to_route('home')->with('success', 'Order Placed Successfully');
                }
            } else {
                $customer_id = User::create([
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'address' => $request->customer_address,
                    'password' => Hash::make($request->customer_phone),
                ]);
            }

            /**---------------------------------------
             * Create Order
             * ---------------------------------------
             */
            $order_input = array_merge($request->all(), [
                'invoice_id' => $invoice_id,
                'order_date' => \Illuminate\Support\Facades\Date::now()->toDateString(),
                'customer_id' => $customer_id->id,
                'sub_total' => \Cart::getSubTotal(),
                'total' => (\Cart::getTotal() + $request->shipping_cost),
                'due' => (\Cart::getTotal() + $request->shipping_cost),
                'status' => 2,
                'ip_address' => $ip,
                'source' => 'direct',
            ]);
            $order_id = Order::create($order_input);

            $sms = SmsSetting::where('status', $order_id->status)->first();
            //send whatsapp
            if ($sms && $sms->is_whatsapp == 1 && $sms->template_name != null) {
                $this->WpServices->sendOrderWhatsapp($order_id, $sms->template_name, $sms->status);
            }

            //add product into order product table
            foreach ($carts as $item) {
                // dd($item);
                OrderProduct::create([
                    'order_id' => $order_id->id,
                    'product_id' => $item->id,
                    'qty' => $item->quantity,
                    'price' => $item->price,
                    'purchase_cost' => $item->associatedModel->purchase_cost,
                    'attributes' => $item->attributes->count() > 0 ? $item->attributes[0] : null,
                    'attribute_ids' => $item->attributes->count() > 0 ? $item->attributes[1] : null,
                ]);
                $p_id = $item->id;
            }
            /**---------------------------------------
             * Assign Employee
             * ---------------------------------------
             */
            if ($carts->count() == 1) { //if carts have only one product
                $employee_id = UserProducts::join('employees', 'employees.id', 'user_products.user_id')
                    ->where('user_products.product_id', $p_id)
                    ->where('employees.status', 1)
                    ->get();
                //if this product has employee
                if ($employee_id->count() > 0) {
                    foreach ($employee_id as $item) {
                        $i[$item->id] = $item->name;
                    }
                    $i = array_rand($i);

                    OrderAssign::create([
                        'order_id' => $order_id->id,
                        'employee_id' => $i,
                    ]);
                } else {
                    $b = Employee::where('status', 1)->where('start_time', '<=', \Illuminate\Support\Facades\Date::now()->toTimeString())->where('end_time', '>=', \Illuminate\Support\Facades\Date::now()->toTimeString())->get();
                    if ($b->count() > 0) {
                        foreach ($b as $item) {
                            $i[$item->id] = $item->name;
                        }
                        $i = array_rand($i);

                        OrderAssign::create([
                            'order_id' => $order_id->id,
                            'employee_id' => $i,
                        ]);
                    }
                }
            } else {
                //if carts have multiple product
                $b = Employee::where('status', 1)->where('start_time', '<=', \Illuminate\Support\Facades\Date::now()->toTimeString())->where('end_time', '>=', \Illuminate\Support\Facades\Date::now()->toTimeString())->get();
                if ($b->count() > 0) {
                    foreach ($b as $item) {
                        $i[$item->id] = $item->name;
                    }
                    $i = array_rand($i);

                    OrderAssign::create([
                        'order_id' => $order_id->id,
                        'employee_id' => $i,
                    ]);
                }
            }
            /**---------------------------------------
             * Fake Customer Checker
             * ---------------------------------------
             */
            if (session()->has('fake_checker')) {
                DB::table('orders')->where('id', $order_id->id)->update([
                    'is_fake' => 1,
                ]);

                $chck_fake = Order::where('id', session()->get('fake_checker'))->first();
                if ($chck_fake) {
                    if ($chck_fake->is_fake == 0) {
                        $chck_fake->update([
                            'is_fake' => 1,
                        ]);
                    }
                }
            } else {
                session()->put('fake_checker', $order_id->id);
            }

            /**---------------------------------------
             * API Conversion
             * ---------------------------------------
             */
            foreach ($order_id->get_products as $key => $get_product) {
                $order_prod[$key] = [
                    'index' => $key,
                    'item_id' => $get_product->get_product->id,
                    'item_category' => count($get_product->get_product->get_categories) > 0 ? $get_product->get_product->get_categories[0]->category_name : "",
                    'item_name' => $get_product->get_product->name,
                    'price' => $get_product->get_product->sale_price ? number_format($get_product->get_product->sale_price, 2, '.', '') : number_format($get_product->get_product->price, 2, '.', ''),
                    'quantity' => $get_product->qty,
                ];
            }
            $api_data = [
                'customer_id' => $order_id->customer_id,
                'full_name' => $order_id->customer_name,
                'phone' => $order_id->customer_phone,
                'email' => $order_id->customer_email,
                'address_summary' => $order_id->customer_address,
                'invoice_id' => $order_id->invoice_id,
                'sub_total' => $order_id->sub_total,
                'shipping_cost' => $order_id->shipping_cost,
                'products' => json_encode($order_prod),
            ];

            session()->put('api_purchase_data', $api_data);

            /**---------------------------------------
             * Abandoned & Cart Clear
             * ---------------------------------------
             */
            if (session()->has('abandoned_cart_id')) {
                //update abandoned cart
                $abandoned = AbandonedCart::where('id', session()->get('abandoned_cart_id'))->first();
                $abandoned->delete();
                session()->forget('abandoned_cart_id');
            }
            // clear the cart
            \Cart::clear();

            $order_info = [
                'name' => $request->customer_name,
                'order_id' => $order_id->invoice_id,
                'total' => $order_id->total,
            ];

            $emp = DB::table('employees')->where('id', $i)->select('name')->first();
            // Create transaction
            order_transaction(
                'local',
                $order_id->id,
                strtr(config('transaction_texts.new_order'), [
                    '{user_name}' => $request->customer_name,
                    '{role}' => 'customer',
                    '{employee_name}' => $emp->name,
                ]),
                null,
                'customer',
                $customer_id->id,
                $i
            );
            return to_route('confirm.order')->with('success', 'Order Placed Successfully')->with('order_info', $order_info);
        } else {
            return to_route('home')->with('error', 'Please Select Products');
        }
    }

    public function confirmOrder()
    {
        $settings = DB::table('web_settings')->where('id', 1)->first();

        if ($settings->fb_pixel_id) {

            if (!empty(\Illuminate\Support\Facades\Request::server('HTTP_CLIENT_IP'))) {
                $user_ip = \Illuminate\Support\Facades\Request::server('HTTP_CLIENT_IP');
            } elseif (!empty(\Illuminate\Support\Facades\Request::server('HTTP_X_FORWARDED_FOR'))) {
                $user_ip = \Illuminate\Support\Facades\Request::server('HTTP_X_FORWARDED_FOR');
            } else {
                $user_ip = \Illuminate\Support\Facades\Request::server('REMOTE_ADDR');
            }

            // Get current page
            $actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

            // Generate Json code to provide
            $data = '{
                    "data": [
                                {
                                    "action_source": "website",
                                    "event_name": "Purchase",
                                    "event_time": ' . time() . ',
                                    "event_source_url": "' . $actual_link . '",
                                    "user_data": {
                                        "fn": ["' . hash('sha256', session('order_info')['name']) . '"],
                                        "country": ["' . hash('sha256', 'BD') . '"],
                                        "ph": ["' . hash('sha256', session('order_info')['phone']) . '"],
                                        "external_id": ["' . hash('sha256', session('order_info')['user_id']) . '"],
                                        "client_ip_address": "' . $user_ip . '",
                                        "client_user_agent": "' . \Illuminate\Support\Facades\Request::server('HTTP_USER_AGENT') . '"
                                    },
                                    "custom_data": {
                                        "currency": "BDT",
                                        "value": "' . session('order_info')['total'] . '"
                                    }
                                }
                            ]
                    }';

            // Set the Facebook Conversions API URL
            $url = "https://graph.facebook.com/v17.0/" . $settings->fb_pixel_id . "/events";
            $_curl_ = new ConversionAPI();
            $_curl_->post_request($url, $data);
        }
        //dd(session()->all());

        return view('frontEnd.order_confirmed');
    }

    public function trackOrder(Request $request)
    {
        visitor()->visit();
        $query = $request->q;
        if ($query) {
            $data = Order::with('get_products')
                //->where('invoice_id', 'LIKE', "%{$query}%")
                ->where('customer_phone', $query)
                ->orderBy('invoice_id', 'desc')->get();
        } else {
            $data = [];
        }
        //dd($data);
        return view('frontEnd.track_order', compact('data'));
    }

    public function search(Request $request)
    {
        visitor()->visit();
        //dd($request->all());
        if ($request->input('category')) {
            $query = $request->input('query');
            $data = DB::table('category_products')
                ->select('products.name', 'products.id as product_id', 'products.thumb', 'products.slug', 'products.price', 'products.sale_price', 'media.file_url')
                ->leftJoin('products', 'products.id', 'category_products.product_id')
                ->leftJoin('media', 'media.id', 'products.thumb')
                ->where([['category_products.category_id', $request->input('category')], ['products.status', 1]])
                ->where('products.name', 'LIKE', "%{$query}%")
                ->paginate(35);
        } else {
            $query = $request->input('query');
            $data = DB::table('products')
                ->select('products.name', 'products.id as product_id', 'products.thumb', 'products.slug', 'products.price', 'products.sale_price', 'media.file_url')
                ->leftJoin('media', 'media.id', 'products.thumb')
                ->where([['products.name', 'LIKE', "%{$query}%"], ['products.status', 1]])
                ->paginate(35);
        }

        //dd($data);
        return view('frontEnd.searched_products', compact('data', 'query'));
    }

    public function statusUpdate(Request $request)
    {

        $json = file_get_contents('php://input');
        $object = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die(header('HTTP/1.0 415 Unsupported Media Type'));
        }

        if ($object['event'] == 'webhook_integration') {
            //for active webhook
            $data = [
                'status' => 'accepted',
                'message' => 'Webhook received successfully'
            ];
            return response()->json($data, 202)->header('X-Pathao-Merchant-Webhook-Integration-Secret', 'f3992ecc-59da-4cbe-a049-a13da2018d51');
        }

        $pathao_settings = DB::table('pathao_apis')->select('id', 'store_id')->first();
        if ($object['store_id'] == $pathao_settings->store_id) { //if matched store id
            if ($object['event'] == 'order.created') {
                file_put_contents(base_path('callback.txt'), $object);
                DB::table('orders')->where('invoice_id', $object['merchant_order_id'])->update([
                    'courier_status' => 'Order Created',
                    'pathao_consignment_id' => $object['consignment_id'],
                    'courier_api_response' => null,
                ]);
            } elseif ($object['event'] == 'order.updated') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                ]);
            } elseif ($object['event'] == 'order.pickup-requested') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                ]);
            } elseif ($object['event'] == 'order.assigned-for-pickup') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                ]);
            } elseif ($object['event'] == 'order.picked') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                ]);
            } elseif ($object['event'] == 'order.pickup-failed') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                ]);
            } elseif ($object['event'] == 'order.pickup-cancelled') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                ]);
            } elseif ($object['event'] == 'order.at-the-sorting-hub') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                    'status' => 6,
                ]);
            } elseif ($object['event'] == 'order.in-transit') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                    'status' => 6,
                ]);
            } elseif ($object['event'] == 'order.received-at-last-mile-hub') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                ]);
            } elseif ($object['event'] == 'order.assigned-for-delivery') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                ]);
            } elseif ($object['event'] == 'order.delivered') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'status' => 1,
                    'courier_status' => $object['order_status'],
                ]);
            } elseif ($object['event'] == 'order.partial-delivery') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                    'courier_status_reason' => $object['reason'],
                ]);
            } elseif ($object['event'] == 'order.returned') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'status' => 7,
                    'courier_status' => $object['order_status'],
                    'courier_status_reason' => $object['reason'],
                ]);
            } elseif ($object['event'] == 'order.delivery-failed') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                    'courier_status_reason' => $object['reason'],
                ]);
            } elseif ($object['event'] == 'order.on-hold') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                    'courier_status_reason' => $object['reason'],
                ]);
            } elseif ($object['event'] == 'order.paid') {
                $order_id = DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                    'courier_status_reason' => $object['invoice_id'],
                    'payment_status' => 2,
                ]);

                order_transaction(
                    'api',
                    $order_id->id,
                    'Payment status changed into Paid by Pathao API',
                    null,
                    'API',
                    null,
                    null
                );
            } elseif ($object['event'] == 'order.paid-return') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                    'courier_status_reason' => $object['reason'],
                ]);
            } elseif ($object['event'] == 'order.exchanged') {
                DB::table('orders')->where('pathao_consignment_id', $object['consignment_id'])->update([
                    'courier_status' => $object['order_status'],
                    'courier_status_reason' => $object['reason'],
                ]);
            }
        }
        return response()->json()->header('X-Pathao-Merchant-Webhook-Integration-Secret', 'f3992ecc-59da-4cbe-a049-a13da2018d51');
    }

    public function redxStatusUpdate(Request $request)
    {
        $json = file_get_contents('php://input');
        $object = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die(header('HTTP/1.0 415 Unsupported Media Type'));
        }
        //file_put_contents(base_path('callback.txt'), $object['consignment_id']);

        if ($object['status'] == 'delivered') {
            DB::table('orders')->where('redx_tracking_id', $object['tracking_number'])->update([
                'status' => 1,
            ]);
        } elseif ($object['status'] == 'returned') {
            DB::table('orders')->where('redx_tracking_id', $object['tracking_number'])->update([
                'status' => 7,
            ]);
        }
    }

    public function carryBeeStatusUpdate(Request $request)
    {
        //file_put_contents(base_path('callback.txt'), 'do');
        $json = file_get_contents('php://input');
        file_put_contents(base_path('callback.txt'), $json);
        $object = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die(header('HTTP/1.0 415 Unsupported Media Type'));
        }


        if ($object['order_status_slug'] == 'Picked') {
            file_put_contents(base_path('callbacks.txt'), $json);
            DB::table('orders')->where('invoice_id', $object['merchant_order_id'])->update([
                'courier_status' => 'Order Created',
                'carrybee_consignment_id' => $object['consignment_id'],
            ]);
        } elseif ($object['order_status_slug'] == 'Pickup_Requested') {
            file_put_contents(base_path('callbackss.txt'), $json);
            DB::table('orders')->where('invoice_id', $object['merchant_order_id'])->update([
                'courier_status' => 'Order Created',
                'carrybee_consignment_id' => $object['consignment_id'],
            ]);
        } elseif ($object['order_status_slug'] == 'Delivered') {
            DB::table('orders')->where('carrybee_consignment_id', $object['consignment_id'])->update([
                'status' => 1,
            ]);
        } elseif ($object['order_status_slug'] == 'Return') {
            DB::table('orders')->where('carrybee_consignment_id', $object['consignment_id'])->update([
                'status' => 8,
            ]);
        }

        //62gb0TjPkKaNsbF9MNWZoR7
        return response()->json()
            ->header('X-BEE-Signature', 'vN3In6FmNY01M2Vjc3n')
            ->header('Accept', 'application/json')
            ->header('Content-Type', 'application/json')
            ->header('Content-Length', 185);
    }

    public function abandonedCart(Request $request)
    {
        $carts = \Cart::getContent();
        $abandoned_item = [];
        foreach ($carts as $key => $item) {
            $abandoned_item[$key] = [
                'product_id' => $item->id,
                'qty' => $item->quantity,
                'price' => $item->price,
                'attributes' => $item->attributes->count() > 0 ? $item->attributes[0] : null,
                'attribute_ids' => $item->attributes->count() > 0 ? $item->attributes[1] : null,
            ];
        }
        $abandoned_item = json_encode($abandoned_item);
        $input = [
            'customer_name' => $request->data['name'],
            'customer_phone' => $request->data['phone'],
            'customer_address' => $request->data['address'],
            'shipping_cost' => $request->data['shipping_cost'],
            'total' => \Cart::getTotal() + $request->data['shipping_cost'],
            'subtotal' => \Cart::getSubTotal(),
            'abandoned_item' => $abandoned_item,
        ];
        // dd(session()->get('abandoned_cart_id'));
        if (session()->has('abandoned_cart_id')) {
            $abandoned = AbandonedCart::where('id', session()->get('abandoned_cart_id'))->first();
            if ($abandoned) {
                $abandoned->update($input);
            } else {
                $id = AbandonedCart::create($input);
                session()->put('abandoned_cart_id', $id->id);
            }
        } else {
            $id = AbandonedCart::create($input);
            session()->put('abandoned_cart_id', $id->id);
        }
    }

    public function productsCatalogFeed(Request $request)
    {
        $products = Product::with('get_image')->select(
            'id',
            'name',
            'fb_description',
            'slug',
            'image',
            'price',
            'sale_price',
            'brand_name'
        )->orderBy('id', 'desc')
            ->get();

        return response()->view('frontEnd.product_catalog_feed', [
            'products' => $products,
        ])->header('Content-Type', 'text/xml');
    }
}
