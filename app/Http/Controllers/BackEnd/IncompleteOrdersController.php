<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAbandonedCartNoteRequest;
use App\Models\AbandonedCart;
use App\Services\AbandonedCartForwardingService;
use App\Services\AbandonedCartOrderCreator;
use App\Services\OrderForwardingService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncompleteOrdersController extends Controller
{
    public function __construct(
        private AbandonedCartForwardingService $abandonedCartForwardingService,
    ) {}

    public function index(): View
    {
        $data = AbandonedCart::with('assignedEmployee')
            ->when(Auth::guard('employee')->check(), function ($query) {
                $query->where('employee_id', Auth::guard('employee')->id());
            })
            ->latest()
            ->paginate(10);

        return view('backEnd.admin.incomplete-orders.index', [
            'data' => $data,
            'employees' => \App\Models\Employee::query()
                ->where('status', 1)
                ->orderBy('name')
                ->pluck('name', 'id'),
        ]);
    }

    public function createOrder(int $id, AbandonedCartOrderCreator $creator, OrderForwardingService $orderForwardingService): RedirectResponse
    {
        $cart = AbandonedCart::query()->findOrFail($id);
        $order = $creator->createFromAbandonedCart($cart);

        $orderForwardingService->forwardIfConfigured($order);

        return back()->with('success', 'Order Created Successfully From Incomplete Order');
    }

    public function delete(int $id): RedirectResponse
    {
        if (! Auth::guard('admin')->check()) {
            throw new AuthorizationException('Only admin can delete incomplete orders');
        }

        $cart = AbandonedCart::query()->findOrFail($id);
        $this->abandonedCartForwardingService->notifyMasterOfDeletion($cart);
        $cart->delete();

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
        $this->abandonedCartForwardingService->syncToMaster($cart->fresh());

        return back()->with('success', 'Incomplete Order Cancelled Successfully');
    }

    public function assignEmployee(int $id, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
        ]);

        $cart = AbandonedCart::query()->findOrFail($id);
        $cart->update([
            'employee_id' => $validated['employee_id'] ?? null,
        ]);
        $this->abandonedCartForwardingService->syncToMaster($cart->fresh());

        return back()->with('success', 'Assigned employee updated successfully');
    }

    public function bulkAssignEmployee(Request $request): RedirectResponse
    {
        if (! Auth::guard('admin')->check()) {
            throw new AuthorizationException('Only admin can bulk assign incomplete orders');
        }

        $validated = $request->validate([
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:abandoned_carts,id'],
        ]);

        AbandonedCart::query()
            ->whereIn('id', $validated['ids'])
            ->update([
                'employee_id' => $validated['employee_id'] ?? null,
            ]);

        foreach ($validated['ids'] as $cartId) {
            $cart = AbandonedCart::query()->find((int) $cartId);
            if ($cart) {
                $this->abandonedCartForwardingService->syncToMaster($cart);
            }
        }

        return back()->with('success', 'Selected incomplete orders updated successfully');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        if (! Auth::guard('admin')->check()) {
            throw new AuthorizationException('Only admin can bulk delete incomplete orders');
        }

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:abandoned_carts,id'],
        ]);

        foreach ($validated['ids'] as $cartId) {
            $cart = AbandonedCart::query()->find((int) $cartId);
            if ($cart) {
                $this->abandonedCartForwardingService->notifyMasterOfDeletion($cart);
            }
        }

        AbandonedCart::query()
            ->whereIn('id', $validated['ids'])
            ->delete();

        return back()->with('success', 'Selected incomplete orders deleted successfully');
    }

    public function noteUpdate(UpdateAbandonedCartNoteRequest $request): RedirectResponse
    {
        $cart = AbandonedCart::query()->findOrFail($request->validated()['id']);
        $cart->update([
            'note' => $request->validated()['note'],
        ]);
        $this->abandonedCartForwardingService->syncToMaster($cart->fresh());

        return back()->with('success', 'Note Updated Successfully');
    }
}
