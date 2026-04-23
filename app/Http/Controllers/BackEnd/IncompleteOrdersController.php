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
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IncompleteOrdersController extends Controller
{
    public function __construct(
        private AbandonedCartForwardingService $abandonedCartForwardingService,
    ) {}

    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $paginate = (int) $request->query('paginate', 25);

        $baseQuery = AbandonedCart::query()
            ->when(Auth::guard('employee')->check(), function ($query) {
                $query->where('employee_id', Auth::guard('employee')->id());
            });

        $data = (clone $baseQuery)
            ->with('assignedEmployee')
            ->when($statusFilter !== null && in_array((string) $statusFilter, ['0', '1'], true), function ($query) use ($statusFilter) {
                $query->where('status', (int) $statusFilter);
            })
            ->latest()
            ->paginate($paginate)
            ->withQueryString();

        return view('backEnd.admin.incomplete-orders.index', [
            'data' => $data,
            'paginate' => $paginate,
            'totalIncompleteCount' => (clone $baseQuery)->count(),
            'activeIncompleteCount' => (clone $baseQuery)->where('status', 0)->count(),
            'cancelledIncompleteCount' => (clone $baseQuery)->where('status', 1)->count(),
            'statusFilter' => $statusFilter !== null ? (string) $statusFilter : null,
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

    public function bulkCancel(Request $request): RedirectResponse
    {
        if (! Auth::guard('admin')->check()) {
            throw new AuthorizationException('Only admin can bulk cancel incomplete orders');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:5'],
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:abandoned_carts,id'],
        ], [
            'reason.required' => 'Cancellation reason is required',
            'reason.min' => 'Cancellation reason must be at least 5 characters',
        ]);

        AbandonedCart::query()
            ->whereIn('id', $validated['ids'])
            ->update([
                'status' => 1,
                'note' => $validated['reason'],
            ]);

        foreach ($validated['ids'] as $cartId) {
            $cart = AbandonedCart::query()->find((int) $cartId);
            if ($cart) {
                $this->abandonedCartForwardingService->syncToMaster($cart);
            }
        }

        return back()->with('success', 'Selected incomplete orders cancelled successfully');
    }

    public function bulkEqualAssign(Request $request): RedirectResponse
    {
        if (! Auth::guard('admin')->check()) {
            throw new AuthorizationException('Only admin can equal assign incomplete orders');
        }

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:abandoned_carts,id'],
        ]);

        $activeEmployees = \App\Models\Employee::query()
            ->where('status', 1)
            ->orderBy('id')
            ->get(['id']);

        if ($activeEmployees->count() === 0) {
            return back()->with('error', 'No active employees found');
        }

        $activeCarts = AbandonedCart::query()
            ->whereIn('id', $validated['ids'])
            ->where('status', 0)
            ->orderBy('id')
            ->get(['id']);

        if ($activeCarts->count() === 0) {
            return back()->with('warning', 'No active incomplete orders selected');
        }

        $perEmployeeCarts = (int) ceil($activeCarts->count() / $activeEmployees->count());
        $skip = 0;

        DB::transaction(function () use ($activeEmployees, $activeCarts, $perEmployeeCarts, &$skip): void {
            foreach ($activeEmployees as $activeEmployee) {
                $chunkIds = $activeCarts->skip($skip)->take($perEmployeeCarts)->pluck('id')->map(fn ($id) => (int) $id)->all();

                if (count($chunkIds) === 0) {
                    break;
                }

                AbandonedCart::query()
                    ->whereIn('id', $chunkIds)
                    ->update([
                        'employee_id' => (int) $activeEmployee->id,
                    ]);

                $skip += $perEmployeeCarts;
            }
        });

        foreach ($activeCarts->pluck('id') as $cartId) {
            $cart = AbandonedCart::query()->find((int) $cartId);
            if ($cart) {
                $this->abandonedCartForwardingService->syncToMaster($cart);
            }
        }

        return back()->with('success', 'Equal assign completed for selected active incomplete orders');
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
