<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoogleSheetSettings extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'all_order_sheet_id',
    ];
}
