<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPage extends Model
{
    /** @use HasFactory<\Database\Factories\LandingPageFactory> */
    use HasFactory;

    protected $fillable = [
        'product_id', 'title', 'subtitle', 'slug', 'banner_image',
        'about_section_head', 'about_section_body', 'gallery_images',
        'gallery_section_head', 'why_section_head', 'why_section_body', 'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function bannerMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'banner_image');
    }

    // Get display banner image - fallback to product thumbnail
    public function getDisplayBannerAttribute(): ?string
    {
        if ($this->bannerMedia && $this->bannerMedia->file_url) {
            return asset($this->bannerMedia->file_url);
        }

        if ($this->product && $this->product->get_thumb && $this->product->get_thumb->file_url) {
            return asset($this->product->get_thumb->file_url);
        }

        return asset('frontEnd/images/no_image.png');
    }

    // Get gallery images - fallback to product gallery
    protected function galleryImagesArray(): Attribute
    {
        return Attribute::make(get: function () {
            // Use landing page gallery if available
            if ($this->gallery_images) {
                $ids = collect(explode(',', (string) $this->gallery_images))
                    ->filter()
                    ->map(fn ($id) => (int) $id)
                    ->filter();

                if ($ids->isNotEmpty()) {
                    $mediaById = Media::query()
                        ->whereIn('id', $ids->all())
                        ->get(['id', 'file_url'])
                        ->keyBy('id');

                    return $ids->map(fn (int $id) => $mediaById->get($id)?->file_url)
                        ->filter()
                        ->map(fn ($url) => asset($url))
                        ->values()
                        ->all();
                }
            }

            // Fallback to product gallery images
            if ($this->product && $this->product->gallery_images) {
                return collect($this->product->images)
                    ->map(fn ($url) => asset($url))
                    ->all();
            }

            return [];
        });
    }

    // Get display about section head - fallback to "About Product"
    public function getDisplayAboutHeadAttribute(): string
    {
        return $this->about_section_head ?: 'About Product';
    }

    // Get display why section head - fallback to "Why This Product?"
    public function getDisplayWhyHeadAttribute(): string
    {
        return $this->why_section_head ?: 'Why This Product?';
    }
}
