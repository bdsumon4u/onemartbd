<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = ['slider_image', 'slider_url', 'status'];

    public function media(): HasOne
    {
        return $this->hasOne(Media::class, 'id', 'slider_image');
    }

    // Backward-compatible accessor
    public function get_img(): HasOne
    {
        return $this->media();
    }
}
