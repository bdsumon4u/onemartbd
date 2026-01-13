<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\ShippingMethod;
use Illuminate\Http\Request;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $data = ShippingMethod::all();

        return view('backEnd.admin.shipping_methods.index', compact('data'));
    }

    public function store(Request $request)
    {
        ShippingMethod::create($request->all());

        return to_route('admin.shipping_methods')->with('success', 'Shipping Method Added Successfully');
    }

    public function update(Request $request)
    {
        ShippingMethod::find($request->id)->update($request->all());

        return to_route('admin.shipping_methods')->with('success', 'Shipping Method Updated Successfully');
    }

    public function delete($id)
    {
        $has_method = Order::where('shipping_method', $id)->first();
        if ($has_method) {
            return back()->with('warning', 'This Shipping Method Already In Order');
        } else {
            ShippingMethod::find($id)->delete();

            return back()->with('success', 'Shipping Method Deleted Successfully');
        }
    }
}
