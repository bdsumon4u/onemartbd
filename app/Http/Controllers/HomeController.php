<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\Section;
use App\Models\Slider;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        visitor()->visit();

        $categories = Category::where('status', 1)->whereNull('parent')->get();
        $products = Product::with('get_thumb')->where('status', 1)->orderBy('id', 'desc')->paginate(30);
        $hot_deal_1 = Product::with('get_thumb')->where([['sale_price', '>', 0], ['status', 1]])->take(12)->orderBy('id', 'desc')->get();
        $hot_deal_2 = Product::with('get_thumb')->where([['sale_price', '>', 0], ['status', 1]])->skip(12)->take(12)->orderBy('id', 'desc')->get();
        $sliders = Slider::with('get_img')->where('status', 1)->get();
        $categoryProducts = Category::with('get_products')->where('parent', null)->get();
        $sections = Section::with(['activeProducts' => function ($query): void {
            $query->with('get_thumb');
        }])->where('status', 1)->orderBy('sort_order')->get();
        $best_selling = OrderProduct::with('get_product')
            ->select('product_id', DB::raw('count(*) as total'))
            ->whereHas('get_order', function ($query): void {
                $query->where('created_at', '>=', \Illuminate\Support\Facades\Date::now()->subDays(7));
            })
            ->groupBy('product_id')
            ->orderBy('total', 'desc')
            ->take(10)
            ->get();

        return view('frontEnd.index', compact('products', 'hot_deal_1', 'hot_deal_2', 'categories', 'sliders', 'categoryProducts', 'best_selling', 'sections'));
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
}
