<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParcelHandover extends Model
{
    protected $fillable = [
        'invoice_no', 'customer_name', 'customer_phone', 'customer_address', 'total'
    ];
}
