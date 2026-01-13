<?php

namespace App\Http\Controllers;

use App\Exports\CustomerExport;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query');
        if ($query) {
            $data = User::where('phone', 'LIKE', "%{$query}%")->orderBy('id', 'desc')->paginate(100);

        } else {
            $data = User::orderBy('id', 'desc')->paginate(100);
        }

        return view('backEnd.admin.customers.index', compact('data'));
    }

    public function customerExport(Request $request)
    {
        $file_name = 'customers'.'_'.date('d-M-Y').'.xlsx';

        return Excel::download(new CustomerExport(explode(',', $request->all_ord_id)), $file_name);
    }

    public function status($id, $status)
    {
        User::find($id)->update([
            'status' => $status,
        ]);

        return back()->with('success', 'Status changed successfully');
    }
}
