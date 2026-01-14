<?php

namespace App\Http\Controllers\BackEnd;

use App\Enums\IpStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\IpIndexRequest;
use App\Http\Requests\IpSearchRequest;
use App\Models\IP;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IpController extends Controller
{
    public function index(IpIndexRequest $request): View
    {
        $query = $request->validated()['query'] ?? null;

        $builder = IP::query()->latest();
        if (filled($query)) {
            $builder->where('ip_address', 'like', "%{$query}%");
        } else {
            $builder->with('get_orders');
        }

        $data = $builder->paginate(50)->withQueryString();

        return view('backEnd.admin.ip.index', compact('data', 'query'));
    }

    public function search(IpSearchRequest $request): View|RedirectResponse
    {
        $query = $request->validated()['query'];
        $data = IP::query()->where('ip_address', $query)->first();

        if (! $data) {
            return back()->with('error', 'IP not found.');
        }

        $total_orders = $data->orders()->count();

        return view('backEnd.admin.ip.index_search', compact('data', 'query', 'total_orders'));
    }

    public function ipStatus(int $id, int $status): RedirectResponse
    {
        $statusEnum = IpStatus::tryFrom($status);
        if (! $statusEnum) {
            return back()->with('error', 'Invalid status.');
        }

        IP::query()->findOrFail($id)->update([
            'status' => $statusEnum->value,
        ]);

        return back();
    }
}
