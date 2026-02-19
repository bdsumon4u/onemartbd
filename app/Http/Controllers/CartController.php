<?php

namespace App\Http\Controllers;

use App\Models\AbandonedCart;
use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Services\AbandonedCartEmployeeAssigner;
use Darryldecode\Cart\Facades\CartFacade;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private AbandonedCartEmployeeAssigner $employeeAssigner) {}

    public function add(Request $request, $id)
    {
        $product = Product::with('get_categories')->find($id);
        $quantity = $request->qty ?? 1;
        $attributes = $this->processAttributes($request);

        $this->addOrUpdateCart($id, $product, $quantity, $attributes);

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
        return $this->updateQuantity($request->id, 1);
    }

    public function decrementQuantity(Request $request)
    {
        return $this->updateQuantity($request->id, -1);
    }

    public function clear()
    {
        CartFacade::clear();

        return back();
    }

    public function getShippingMethod(Request $request)
    {
        $cartItems = CartFacade::getContent();

        // If cart has single item with active promotion, shipping is free
        if ($cartItems->count() === 1) {
            $item = $cartItems->first();
            if ($item['associatedModel']['start_date'] && $item['associatedModel']['end_date']) {
                return response()->json(0);
            }
        }

        $amount = ShippingMethod::find($request->id)->amount;

        return response()->json($amount);
    }

    public function abandonedCart(Request $request)
    {
        $abandonedItems = $this->formatAbandonedCartItems(CartFacade::getContent());
        $cartData = $this->prepareAbandonedCartData($request->data, $abandonedItems);

        $this->saveOrUpdateAbandonedCart($cartData);
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

    private function addOrUpdateCart(int $id, Product $product, int $quantity, ?array $attributes): void
    {
        $price = $this->getProductPrice($product);

        if (CartFacade::get($id)) {
            CartFacade::update($id, [
                'quantity' => ['relative' => false, 'value' => $quantity],
                'attributes' => $attributes,
            ]);
        } else {
            CartFacade::add([
                'id' => $product->id,
                'name' => $product->name,
                'price' => $price,
                'quantity' => $quantity,
                'attributes' => $attributes,
                'associatedModel' => $product,
            ]);
        }
    }

    private function updateQuantity(int $id, int $change)
    {
        if (CartFacade::getContent()->isEmpty()) {
            return back();
        }

        CartFacade::update($id, ['quantity' => $change]);

        return view('frontEnd.order_info_table')->render();
    }

    private function formatAbandonedCartItems($cartItems): array
    {
        $items = [];

        foreach ($cartItems as $key => $item) {
            $items[$key] = [
                'product_id' => $item->id,
                'qty' => $item->quantity,
                'price' => $item->price,
                'attributes' => $item->attributes->isNotEmpty() ? $item->attributes[0] : null,
                'attribute_ids' => $item->attributes->isNotEmpty() ? $item->attributes[1] : null,
            ];
        }

        return $items;
    }

    private function prepareAbandonedCartData(array $requestData, array $abandonedItems): array
    {
        return [
            'customer_name' => $requestData['name'],
            'customer_phone' => $requestData['phone'],
            'customer_address' => $requestData['address'],
            'shipping_cost' => $requestData['shipping_cost'],
            'total' => CartFacade::getTotal() + $requestData['shipping_cost'],
            'subtotal' => CartFacade::getSubTotal(),
            'abandoned_item' => json_encode($abandonedItems),
        ];
    }

    private function saveOrUpdateAbandonedCart(array $data): void
    {
        if (session()->has('abandoned_cart_id')) {
            $abandoned = AbandonedCart::find(session('abandoned_cart_id'));
            if ($abandoned) {
                $abandoned->update($data);

                return;
            }
        }

        $cart = AbandonedCart::create($data);
        $this->employeeAssigner->assignEmployeeToAbandonedCart($cart);
        session()->put('abandoned_cart_id', $cart->id);
    }

    private function getProductPrice(Product $product): float
    {
        return $product->sale_price > 0 ? $product->sale_price : $product->price;
    }

    private function formatPrice(float $price): string
    {
        return number_format($price, 2, '.', '');
    }

    private function storeConversionApiData(Product $product, int $quantity): void
    {
        $price = $this->getProductPrice($product);
        $formattedPrice = $this->formatPrice($price);

        $productData = [
            'item_id' => $product->id,
            'item_name' => $product->name,
            'item_category' => $product->get_categories[0]->category_name ?? '',
            'price' => $formattedPrice,
            'quantity' => $quantity,
        ];

        session()->put('api_add_to_cart_data', [
            'value' => $formattedPrice,
            'products' => json_encode([$productData]),
        ]);
    }
}
