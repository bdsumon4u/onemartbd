<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    protected $fillable = [
        'type', // api or local
        'order_id',
        'text',
        'comment',
        'created_by',
        'created_by_id',
        'assigned_to',
    ];
}
