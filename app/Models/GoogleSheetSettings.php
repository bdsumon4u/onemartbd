<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleSheetSettings extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['all_order_sheet_id'];
}
