<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\Order;
use App\Models\OrderAssign;
use App\Notifications\NewOrderNotification;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class OrderObserver implements ShouldHandleEventsAfterCommit
{
    public function created(Order $order): void
    {
        $notification = new NewOrderNotification($order);

        $this->notifyAdmins($notification);
        $this->notifyAssignedStaff($order, $notification);
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
