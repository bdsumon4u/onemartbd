<?php

namespace App\Http\Controllers\BackEnd;

use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('query');

        $data = User::query()
            ->when($search, fn ($q) => $q->where('phone', 'like', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(100);

        return view('backEnd.admin.customers.index', compact('data'));
    }

    public function customerExport(Request $request)
    {
        $file_name = 'customers_'.now()->format('d-M-Y').'.xlsx';

        return Excel::download(new CustomerExport(explode(',', $request->all_ord_id)), $file_name);
    }

    public function status($id, $status)
    {
        User::whereKey($id)->update(['status' => (int) $status]);

        return back()->with('success', 'Status changed successfully');
    }
}
