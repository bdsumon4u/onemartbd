<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\IP;
use App\Models\Order;
use Illuminate\Http\Request;

class IpController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('query') ?? null;
        $data = IP::query();
        if ($query) {
            $data = $data->where('ip_address', 'LIKE', "%{$request->input('query')}%")->paginate(50);
            $total_orders = Order::where('ip_address', $query)->count();
        } else {
            $data = $data->with('get_orders')->orderBy('id', 'desc')->paginate(50);
            $total_orders = Order::where('ip_address', $query)->count();
        }

        // dd($data);
        return view('backEnd.admin.ip.index', compact('data', 'query', 'total_orders'));
    }

    public function search(Request $request)
    {
        $query = $request->input('query') ?? null;
        $data = IP::where('ip_address', 'LIKE', "%{$request->input('query')}%")->first();
        $total_orders = Order::where('ip_address', $query)->count();

        return view('backEnd.admin.ip.index_search', compact('data', 'query', 'total_orders'));
    }

    public function ipStatus($id, $status)
    {
        IP::find($id)->update([
            'status' => $status,
        ]);

        return back();
    }
}
