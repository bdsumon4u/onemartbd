<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteSettings extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'text',
    ];
}
