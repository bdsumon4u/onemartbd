<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    protected $fillable = [
        'category_name', 'parent', 'status',
    ];

    public function get_products()
    {
        return $this->belongsToMany(Product::class, 'category_products')->where('status', 1)->orderByRaw('
        CASE 
            WHEN products.position = 0 THEN products.id 
            ELSE products.position 
        END DESC
    ');
    }

    public static function tree()
    {
        $all_categories = Category::query();
        if (Auth::guard('admin')->check()) {
            $all_categories = $all_categories->get();
        } else {
            $all_categories = $all_categories->where('status', 1)/* ->orderBy('position','asc') */ ->get();
        }

        $root_categories = $all_categories->whereNull('parent');
        self::formatTree($root_categories, $all_categories);

        return $root_categories;
    }

    public static function formatTree($categories, $allCategories)
    {
        foreach ($categories as $category) {
            $category->children = $allCategories->where('parent', $category->id)->values();
            if ($category->children->isNotEmpty()) {
                self::formatTree($category->children, $allCategories);
            }
        }

        return $category;
    }
}
