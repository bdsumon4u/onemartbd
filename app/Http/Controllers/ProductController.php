<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ConversionAPI;
use Darryldecode\Cart\Facades\CartFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct(
        private ConversionAPI $conversionAPI,
    ) {}

    public function show($slug, $id)
    {
        visitor()->visit();
        $data = Product::with('mediaImage', 'categoryProduct', 'categories', 'attributes.items.attributeItem')
            ->where([['id', $id], ['status', 1]])
            ->firstOrFail();

        $feature_prod = Product::with('thumbnail')
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();

        $related_prod = $this->getRelatedProducts($data);
        $this->storeViewItemConversionData($data);

        $shipping_methods = DB::table('shipping_methods')->where('status', 1)->get();
        $qty = CartFacade::get($id)->quantity ?? 1;

        $this->conversionAPI->trackEvent('ViewContent', [
            'currency' => 'BDT',
            'value' => $this->formatPrice($this->getProductPrice($data)),
            'content_name' => $data->name,
            'content_ids' => [$data->id],
            'content_type' => 'product',
            'page_url' => url()->current(),
        ]);

        return view('frontEnd.single_product', compact('data', 'related_prod', 'feature_prod', 'shipping_methods', 'qty'));
    }

    public function category($id)
    {
        visitor()->visit();
        $category = Category::with('products')->find($id);
        $data = $category->products()->with('thumbnail')->paginate(42);
        $cat_name = $category->category_name;

        return view('frontEnd.single_category', compact('data', 'cat_name'));
    }

    public function hotDeals()
    {
        $data = Product::with('get_thumb')->where([['sale_price', '>', 0], ['status', 1]])->paginate(42);

        return view('frontEnd.all_hot_deals', compact('data'));
    }

    public function search(Request $request)
    {
        visitor()->visit();
        $query = $request->input('query');
        $categoryId = $request->input('category');

        if (! $query) {
            $data = collect();
        } else {
            $data = $this->buildSearchQuery($query, $categoryId)->paginate(35);
        }

        return view('frontEnd.searched_products', compact('data', 'query'));
    }

    private function getRelatedProducts(Product $product)
    {
        $category = Category::with('products')->find($product->categoryProduct->category_id);

        return $category
            ? $category->products()->with('thumbnail')->inRandomOrder()->take(12)->get()
            : collect();
    }

    private function storeViewItemConversionData(Product $product): void
    {
        $price = $this->formatPrice($this->getProductPrice($product));

        $productData = [
            'item_id' => $product->id,
            'item_name' => $product->name,
            'item_category' => $this->getCategoryName($product),
            'price' => $price,
            'quantity' => 1,
        ];

        session()->put('api_view_item_data', [
            'value' => $price,
            'products' => json_encode([$productData]),
        ]);
    }

    private function buildSearchQuery(string $query, ?string $categoryId)
    {
        $baseQuery = DB::table('products')
            ->select(
                'products.name',
                'products.id as product_id',
                'products.thumb',
                'products.slug',
                'products.price',
                'products.sale_price',
                'media.file_url'
            )
            ->leftJoin('media', 'media.id', 'products.thumb')
            ->where('products.status', 1)
            ->where('products.name', 'LIKE', "%{$query}%");

        if ($categoryId) {
            $baseQuery->join('category_products', 'products.id', 'category_products.product_id')
                ->where('category_products.category_id', $categoryId);
        }

        return $baseQuery;
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
