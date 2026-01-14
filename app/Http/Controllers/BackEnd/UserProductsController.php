<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\UserProducts;
use Illuminate\Http\Request;

class UserProductsController extends Controller
{
    public function index()
    {
        $data = Employee::with('get_products')->where('status', 1)->get();

        return view('backEnd.admin.user_products.index', compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'employee_id' => ['required', 'array'],
            'employee_id.*' => ['integer'],
        ]);

        $assignments = collect($validated['employee_id'])
            ->unique()
            ->map(fn ($userId) => [
                'user_id' => $userId,
                'product_id' => $validated['product_id'],
            ])->all();

        UserProducts::where('product_id', $validated['product_id'])->delete();
        UserProducts::insert($assignments);

        return back()->with('success', 'Employee Assigned Successfully');
    }

    public function delete($id)
    {
        UserProducts::where('product_id', $id)->delete();

        return back()->with('success', 'Employee Unassigned Successfully');
    }
}
