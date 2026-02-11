<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sort_order', 'status'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'section_products')
            ->withPivot('sort_order')
            ->orderBy('section_products.sort_order');
    }

    public function activeProducts(): BelongsToMany
    {
        return $this->products()->where('products.status', 1);
    }
}
