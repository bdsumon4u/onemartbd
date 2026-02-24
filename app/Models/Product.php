<?php

namespace App\Models;

use Codebyray\ReviewRateable\Traits\ReviewRateable;
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
    use ReviewRateable;

    protected $fillable = [
        'position', 'sku', 'thumb', 'image', 'gallery_images', 'name', 'slug', 'stock',
        'description', 'purchase_cost', 'price', 'sale_price', 'status', 'start_date',
        'end_date', 'packaging_cost', 'brand_name', 'fb_description',
    ];

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
            $ids = collect(explode(',', (string) $this->gallery_images))
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->filter();

            if ($ids->isEmpty()) {
                return [];
            }

            $mediaById = Media::query()
                ->whereIn('id', $ids->all())
                ->get(['id', 'file_url'])
                ->keyBy('id');

            return $ids
                ->map(fn (int $id) => $mediaById->get($id)?->file_url)
                ->filter()
                ->values()
                ->all();
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
