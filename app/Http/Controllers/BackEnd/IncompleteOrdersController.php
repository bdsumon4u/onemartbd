<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAbandonedCartNoteRequest;
use App\Models\AbandonedCart;
use App\Services\AbandonedCartOrderCreator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncompleteOrdersController extends Controller
{
    public function index(): View
    {
        $data = AbandonedCart::with('assignedEmployee')
            ->latest()
            ->paginate(10);

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
        if (! Auth::guard('admin')->check()) {
            throw new AuthorizationException('Only admin can delete incomplete orders');
        }

        AbandonedCart::query()->findOrFail($id)->delete();

        return back()->with('success', 'Incompleted Order Deleted Successfully');
    }

    public function cancel(int $id, Request $request): RedirectResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5'],
        ], [
            'reason.required' => 'Cancellation reason is required',
            'reason.min' => 'Cancellation reason must be at least 5 characters',
        ]);

        $cart = AbandonedCart::query()->findOrFail($id);
        $cart->update([
            'status' => 1,
            'note' => $request->reason,
        ]);

        return back()->with('success', 'Incomplete Order Cancelled Successfully');
    }

    public function noteUpdate(UpdateAbandonedCartNoteRequest $request): RedirectResponse
    {
        AbandonedCart::query()->findOrFail($request->validated()['id'])->update([
            'note' => $request->validated()['note'],
        ]);

        return back()->with('success', 'Note Updated Successfully');
    }
}
