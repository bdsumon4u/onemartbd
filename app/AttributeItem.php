<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttributeItem extends Model
{
    protected $fillable = [
        'attribute_id', 'item_title',
    ];
}
