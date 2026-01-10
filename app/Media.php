<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'type', 'file_original_name', 'file_url', 'user_id',
    ];
}
