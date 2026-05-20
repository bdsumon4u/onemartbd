<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallAutomationSetting extends Model
{
    protected $table = 'call_automation_settings';

    protected $guarded = [];

    protected $casts = [
        'maintext' => 'string',
        'text1' => 'string',
        'text2' => 'string',
    ];
}
