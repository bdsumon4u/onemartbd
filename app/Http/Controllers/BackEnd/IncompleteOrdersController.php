<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAbandonedCartNoteRequest;
use App\Models\AbandonedCart;
use App\Services\AbandonedCartOrderCreator;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IncompleteOrdersController extends Controller
{
    public function index(): View
    {
        $data = AbandonedCart::latest()->paginate(10);

        return view('backEnd.admin.incomplete-orders.index', compact('data'));
    }

    public function createOrder(int $id, AbandonedCartOrderCreator $creator): RedirectResponse
    {
        $cart = AbandonedCart::query()->findOrFail($id);
        $creator->createFromAbandonedCart($cart);

        return back()->with('success', 'Order Created Successfully From Incomplete Order');
    }

    public function delete(int $id): RedirectResponse
    {
        AbandonedCart::query()->findOrFail($id)->delete();

        return back()->with('success', 'Incompleted Order Deleted Successfully');
    }

    public function noteUpdate(UpdateAbandonedCartNoteRequest $request): RedirectResponse
    {
        AbandonedCart::query()->findOrFail($request->validated()['id'])->update([
            'note' => $request->validated()['note'],
        ]);

        return back()->with('success', 'Note Updated Successfully');
    }
}
