<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\CategoryProduct;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $data = Category::tree();

        return view('backEnd.admin.categories.index', compact('data'));
    }

    public function store(Request $request)
    {
        Category::create($request->all());

        return back()->with('success', 'Category Added Successfully');
    }

    public function update(Request $request)
    {
        Category::find($request->id)->update($request->all());

        return back()->with('success', 'Category Updated Successfully');
    }

    public function delete($id)
    {
        $has_category = CategoryProduct::where('category_id', $id)->first();
        if ($has_category) {
            return back()->with('error', 'This Category Already In Product');
        } else {
            Category::find($id)->delete();

            return back()->with('success', 'Category Deleted Successfully');
        }
    }
}
