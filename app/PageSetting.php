<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageSetting extends Model
{
    protected $fillable = [
        'about_us', 'delivery_policy', 'return_policy'
    ];
}
