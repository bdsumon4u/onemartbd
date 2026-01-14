<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'position', 'sku', 'thumb', 'image', 'gallery_images', 'name', 'slug', 'stock',
        'description', 'purchase_cost', 'price', 'sale_price', 'status', 'start_date',
        'end_date', 'packaging_cost', 'brand_name', 'fb_description',
    ];

    // Relationship methods
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    public function categoryProduct(): HasOne
    {
        return $this->hasOne(CategoryProduct::class, 'product_id');
    }

    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'thumb');
    }

    public function mediaImage(): HasOne
    {
        return $this->hasOne(Media::class, 'id', 'image');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class, 'product_id');
    }

    public function assignedUser(): HasOne
    {
        return $this->hasOne(UserProducts::class, 'product_id');
    }

    protected function images(): Attribute
    {
        return Attribute::make(get: function () {
            if ($this->gallery_images) {
                $photos = explode(',', $this->gallery_images);
            } else {
                $photos = [];
            }
            $p = '';
            foreach ($photos as $photo) {
                $p .= ','.Media::find($photo)->file_url;
            }
            $p = substr($p, 1);

            return explode(',', $p);
        });
    }

    // Backward-compatible accessors
    public function get_categories(): BelongsToMany
    {
        return $this->categories();
    }

    public function get_category(): HasOne
    {
        return $this->categoryProduct();
    }

    public function get_thumb(): BelongsTo
    {
        return $this->thumbnail();
    }

    public function get_image(): HasOne
    {
        return $this->mediaImage();
    }

    public function get_attributes(): HasMany
    {
        return $this->attributes();
    }

    public function is_assigned(): HasOne
    {
        return $this->assignedUser();
    }
}
