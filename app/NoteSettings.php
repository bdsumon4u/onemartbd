<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoteSettings extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'text',
    ];
}
