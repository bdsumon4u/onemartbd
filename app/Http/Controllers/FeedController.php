<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function productsCatalog(Request $request)
    {
        $products = Product::with('get_image')
            ->select('id', 'name', 'fb_description', 'slug', 'image', 'price', 'sale_price', 'brand_name')
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->get();

        return response()->view('frontEnd.product_catalog_feed', [
            'products' => $products,
        ])->header('Content-Type', 'text/xml');
    }
}
