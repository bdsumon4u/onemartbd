<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['category_name', 'parent', 'status'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_products')
            ->where('status', 1)
            ->orderByRaw('CASE WHEN products.position = 0 THEN products.id ELSE products.position END DESC');
    }

    public static function tree(): Collection
    {
        $categoriesByParent = Category::query()
            ->when(! Auth::guard('admin')->check(), fn ($query) => $query->where('status', 1))
            ->get()
            ->groupBy('parent');

        return self::buildTree($categoriesByParent, null);
    }

    protected static function buildTree(Collection $categoriesByParent, ?int $parentId): Collection
    {
        return ($categoriesByParent[$parentId] ?? collect())->map(function ($category) use ($categoriesByParent) {
            $category->children = self::buildTree($categoriesByParent, $category->id);

            return $category;
        });
    }

    // Backward-compatible accessor
    public function get_products(): BelongsToMany
    {
        return $this->products();
    }
}
