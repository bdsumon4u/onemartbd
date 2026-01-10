<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderAssign extends Model
{
    protected $fillable = [
        'order_id', 'employee_id',
    ];

    public function get_order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id')->with('get_products', 'get_courier');
    }

    public function get_employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id')->select('id', 'name');
    }
}
