<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Darryldecode\Cart\Facades\CartFacade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function show($slug, $id)
    {
        visitor()->visit();
        $data = Product::with('mediaImage', 'categoryProduct', 'categories')->where([['id', $id], ['status', 1]])->first();
        abort_unless($data, 404);
        $feature_prod = Product::with('thumbnail')->where('status', 1)->orderBy('id', 'desc')->take(3)->get();

        $category = Category::with('products')->find($data->categoryProduct->category_id);
        if ($category) {
            $related_prod = $category->products()->with('thumbnail')->inRandomOrder()->take(12)->get();
        } else {
            $related_prod = [];
        }

        // for conversion api
        $order_prod[] = [
            'item_id' => $data->id,
            'item_name' => $data->name,
            'item_category' => count($data->get_categories) > 0 ? $data->get_categories[0]->category_name : '',
            'price' => $data->sale_price ? number_format($data->sale_price, 2, '.', '') : number_format($data->price, 2, '.', ''),
            'quantity' => 1,
        ];

        $api_data = [
            'value' => $data->sale_price ? number_format($data->sale_price, 2, '.', '') : number_format($data->price, 2, '.', ''),
            'products' => json_encode($order_prod),
        ];

        session()->put('api_view_item_data', $api_data);

        $shipping_methods = DB::table('shipping_methods')->where('status', 1)->get();
        $qty = CartFacade::get($id)->quantity ?? 1;

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

        return view('frontEnd.searched_products', compact('data', 'query'));
    }
}
