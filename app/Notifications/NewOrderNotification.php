<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, self $notification): WebPushMessage
    {
        $total = number_format((float) $this->order->total, 2);

        return (new WebPushMessage)
            ->title('🛒 New Order #'.$this->order->invoice_id)
            ->icon('/frontEnd/images/no_image.png')
            ->body("Customer: {$this->order->customer_name}\nTotal: ৳{$total}")
            ->action('View Order', 'view_order')
            ->data([
                'order_id' => $this->order->id,
                'invoice_id' => $this->order->invoice_id,
                'type' => 'new_order',
            ])
            ->options(['TTL' => 300, 'urgency' => 'high']);
    }
}
