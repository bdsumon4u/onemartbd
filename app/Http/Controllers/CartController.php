<?php

namespace App\Http\Controllers;

use App\Models\AbandonedCart;
use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Models\Product;
use App\Models\ShippingMethod;
use Darryldecode\Cart\Facades\CartFacade;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, $id)
    {
        $attrb = $this->processAttributes($request);
        $quantity = $request->qty ?? 1;
        $product = Product::with('get_categories')->find($id);
        $price = $product->sale_price > 0 ? $product->sale_price : $product->price;

        if (CartFacade::get($id)) {
            CartFacade::update($id, [
                'quantity' => [
                    'relative' => false,
                    'value' => $quantity,
                ],
                'attributes' => $attrb,
            ]);
        } else {
            CartFacade::add([
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
        }

        if ($request->add_cart) {
            $this->storeConversionApiData($product, $quantity);

            return back()->with(['success' => 'Product Added Into Cart Successfully', 'api_add_to_cart' => 'yes']);
        }

        return back()->with('error', 'Something Went Wrong!');
    }

    public function deleteItem($id)
    {
        CartFacade::remove($id);

        return back();
    }

    public function incrementQuantity(Request $request)
    {
        if (CartFacade::getContent()->count() > 0) {
            CartFacade::update($request->id, ['quantity' => 1]);

            return view('frontEnd.order_info_table')->render();
        }

        return back();
    }

    public function decrementQuantity(Request $request)
    {
        if (CartFacade::getContent()->count() > 0) {
            CartFacade::update($request->id, ['quantity' => -1]);

            return view('frontEnd.order_info_table')->render();
        }

        return back();
    }

    public function clear()
    {
        CartFacade::clear();

        return back();
    }

    public function getShippingMethod(Request $request)
    {
        if (count(CartFacade::getContent()) <= 1) {
            foreach (CartFacade::getContent() as $it) {
                $amount = ($it['associatedModel']['start_date'] && $it['associatedModel']['end_date'])
                    ? 0
                    : ShippingMethod::find($request->id)->amount;
            }
        } else {
            $amount = ShippingMethod::find($request->id)->amount;
        }

        return response()->json($amount);
    }

    public function abandonedCart(Request $request)
    {
        $carts = CartFacade::getContent();
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

        $input = [
            'customer_name' => $request->data['name'],
            'customer_phone' => $request->data['phone'],
            'customer_address' => $request->data['address'],
            'shipping_cost' => $request->data['shipping_cost'],
            'total' => CartFacade::getTotal() + $request->data['shipping_cost'],
            'subtotal' => CartFacade::getSubTotal(),
            'abandoned_item' => json_encode($abandoned_item),
        ];

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

    private function processAttributes(Request $request): ?array
    {
        if (! $request->attribute_id) {
            return null;
        }

        $attr = [[], []];

        foreach ($request->attribute_id as $item) {
            $an = Attribute::find($item)->title;
            $ain = AttributeItem::find($request->attribute_item_id[$item][0])->item_title;
            $attr[0][$an] = $ain;
        }

        foreach ($request->attribute_id as $item) {
            $attr[1][$item] = $request->attribute_item_id[$item][0];
        }

        return [json_encode($attr[0]), json_encode($attr[1])];
    }

    private function storeConversionApiData(Product $product, int $quantity): void
    {
        $order_prod[] = [
            'item_id' => $product->id,
            'item_name' => $product->name,
            'item_category' => count($product->get_categories) > 0 ? $product->get_categories[0]->category_name : '',
            'price' => $product->sale_price ? number_format($product->sale_price, 2, '.', '') : number_format($product->price, 2, '.', ''),
            'quantity' => $quantity,
        ];

        $api_data = [
            'value' => $product->sale_price ? number_format($product->sale_price, 2, '.', '') : number_format($product->price, 2, '.', ''),
            'products' => json_encode($order_prod),
        ];

        session()->put('api_add_to_cart_data', $api_data);
    }
}
