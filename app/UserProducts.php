<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProducts extends Model
{
    protected $fillable = [
        'user_id', 'product_id'
    ];

    public function get_product()
    {
        return $this->hasOne(Product::class,  'id','product_id');
    }
}
