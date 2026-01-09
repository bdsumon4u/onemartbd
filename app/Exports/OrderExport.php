<?php

namespace App\Exports;

use App\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OrderExport implements FromView, ShouldAutoSize
{
    public $data;
    public $status;

    public function __construct($data, $status)
    {
        $this->data = $data;
        $this->status = $status;
    }

    public function view(): View
    {
        $data = Order::with('get_products','get_shipping_method')->find($this->data);
        if ($this->status == 1) {
            $view = 'backEnd.admin.orders.courier_csv.pathao_csv';
        } elseif ($this->status == 2) {
            $view = 'backEnd.admin.orders.courier_csv.redex_csv';
        } elseif ($this->status == 3) {
            $view = 'backEnd.admin.orders.courier_csv.paperfly_csv';
        } elseif ($this->status == 4) {
            $view = 'backEnd.admin.orders.courier_csv.stead_fast_csv';
        } elseif ($this->status == 0) {
            $view = 'backEnd.admin.orders.courier_csv.export_orders';
        }

        return view($view, [
            'data' => $data,
        ]);
    }
}
