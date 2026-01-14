<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status'];

    public function items(): HasMany
    {
        return $this->hasMany(AttributeItem::class);
    }

    // Backward-compatible accessor
    public function get_items(): HasMany
    {
        return $this->items();
    }
}
