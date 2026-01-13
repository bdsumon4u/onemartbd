<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'position',
        'sku',
        'thumb',
        'image',
        'gallery_images',
        'name',
        'slug',
        'stock',
        'description',
        'purchase_cost',
        'price',
        'sale_price',
        'status',
        'start_date',
        'end_date',
        'packaging_cost',
        'brand_name',
        'fb_description',
    ];

    public function get_categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    public function get_category()
    {
        return $this->hasOne(CategoryProduct::class, 'product_id', 'id');
    }

    public function get_thumb()
    {
        return $this->belongsTo(Media::class, 'thumb', 'id');
    }

    public function get_image()
    {
        return $this->hasOne(Media::class, 'id', 'image');
    }

    protected function images(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
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
            $p = explode(',', $p);

            return $p;
        });
    }

    public function get_attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'id')->with('get_attribute', 'get_attribute_items');
    }

    public function is_assigned()
    {
        return $this->hasOne(UserProducts::class, 'product_id', 'id');
    }
}
