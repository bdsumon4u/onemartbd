<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable
{
    protected $fillable = [
        'p_id',
        'name',
        'email',
        'phone',
        'password',
        'status',
        'last_seen',
        'last_login_ip',
    ];

    public function get_products()
    {
        return $this->hasMany(UserProducts::class,'user_id','id')->with('get_product');
    }
}
