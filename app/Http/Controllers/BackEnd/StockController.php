<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;

class StockController extends Controller
{
    public function stock()
    {
        return view('backEnd.admin.stock');
    }
}
