<?php

namespace App\Observers;

use App\Enums\OrderStatus;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\Order;
use App\Models\OrderAssign;
use App\Notifications\NewOrderNotification;
use App\Services\OrderForwardingService;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Date;

class OrderObserver implements ShouldHandleEventsAfterCommit
{
    public function __construct(private OrderForwardingService $orderForwardingService) {}

    public function created(Order $order): void
    {
        $notification = new NewOrderNotification($order);

        $this->notifyAdmins($notification);
        $this->notifyAssignedStaff($order, $notification);
    }

    public function updated(Order $order): void
    {
        $originalStatus = (int) $order->getOriginal('status');
        $currentStatus = (int) $order->status;

        if ($originalStatus === $currentStatus) {
            return;
        }

        if ($currentStatus === OrderStatus::Confirmed->value) {
            $order->forceFill([
                'confirmed_at' => Date::now(),
            ])->saveQuietly();
        }

        $this->orderForwardingService->handleOrderStatusChanged($order, $originalStatus, $currentStatus);
    }

    private function notifyAdmins(NewOrderNotification $notification): void
    {
        Admin::query()
            ->where('status', 1)
            ->whereHas('pushSubscriptions')
            ->each(function (Admin $admin) use ($notification): void {
                $admin->notify($notification);
            });
    }

    private function notifyAssignedStaff(Order $order, NewOrderNotification $notification): void
    {
        $assignment = OrderAssign::query()
            ->where('order_id', $order->id)
            ->first();

        if (! $assignment) {
            return;
        }

        $employee = Employee::query()
            ->where('id', $assignment->employee_id)
            ->where('status', 1)
            ->whereHas('pushSubscriptions')
            ->first();

        if ($employee) {
            $employee->notify($notification);
        }

        $manager = Manager::query()
            ->where('status', 1)
            ->whereHas('pushSubscriptions')
            ->each(function (Manager $manager) use ($notification): void {
                $manager->notify($notification);
            });
    }
}
