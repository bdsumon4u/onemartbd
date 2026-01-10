<?php

namespace App\Http\Controllers;

use App\Employee;
use App\UserProducts;
use Illuminate\Http\Request;

class UserProductsController extends Controller
{
    public function index()
    {
        $data = Employee::with('get_products')->where('status', 1)->get();

        // dd($data);
        return view('backEnd.admin.user_products.index', compact('data'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        foreach ($request->employee_id as $item) {
            UserProducts::create([
                'user_id' => $item,
                'product_id' => $request->product_id,
            ]);
        }

        return back()->with('success', 'Employee Assigned Successfully');
    }

    public function delete($id)
    {
        // dd($id);
        UserProducts::where('product_id', $id)->delete();

        return back()->with('success', 'Employee Unassigned Successfully');
    }
}
