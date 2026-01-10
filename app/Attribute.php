<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = [
        'title', 'status',
    ];

    public function get_items()
    {
        return $this->hasMany(AttributeItem::class, 'attribute_id', 'id');
    }
}
