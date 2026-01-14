<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrderExport implements FromView, ShouldAutoSize
{
    public function __construct(public $data, public $status) {}

    public function view(): View
    {
        $data = Order::with('products', 'shippingMethod', 'courierCity', 'courierZone')->find($this->data);
        $view = 'backEnd.admin.orders.courier_csv.'.match ($this->status) {
            1 => 'pathao_csv',
            2 => 'redex_csv',
            3 => 'paperfly_csv',
            4 => 'stead_fast_csv',
            default => 'export_orders',
        };

        return view($view, [
            'data' => $data,
        ]);
    }
}
