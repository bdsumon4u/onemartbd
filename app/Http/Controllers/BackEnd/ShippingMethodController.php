<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShippingMethodRequest;
use App\Http\Requests\UpdateShippingMethodRequest;
use App\Models\Order;
use App\Models\ShippingMethod;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $data = ShippingMethod::all();

        return view('backEnd.admin.shipping_methods.index', compact('data'));
    }

    public function store(StoreShippingMethodRequest $request)
    {
        $payload = $request->validated();
        $payload['status'] = (int) $payload['status'];

        ShippingMethod::create($payload);

        return to_route('admin.shipping_methods')->with('success', 'Shipping Method Added Successfully');
    }

    public function update(UpdateShippingMethodRequest $request)
    {
        $payload = $request->validated();
        $payload['status'] = (int) $payload['status'];

        ShippingMethod::query()->findOrFail($payload['id'])->update($payload);

        return to_route('admin.shipping_methods')->with('success', 'Shipping Method Updated Successfully');
    }

    public function delete(int $id)
    {
        if (Order::query()->where('shipping_method', $id)->exists()) {
            return back()->with('warning', 'This Shipping Method Already In Order');
        }

        ShippingMethod::query()->findOrFail($id)->delete();

        return back()->with('success', 'Shipping Method Deleted Successfully');
    }
}
