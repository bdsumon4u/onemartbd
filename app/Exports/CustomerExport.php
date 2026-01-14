<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CustomerExport implements FromView, ShouldAutoSize
{
    public function __construct(public $data) {}

    public function view(): View
    {
        return view('backEnd.admin.customers.export_customers', [
            'data' => User::find($this->data),
        ]);
    }
}
