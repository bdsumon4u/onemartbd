<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    protected $fillable = [
        'name',
        'status',
        'is_active',
        'is_whatsapp',
        'template_name',
        'message',
    ];
}
