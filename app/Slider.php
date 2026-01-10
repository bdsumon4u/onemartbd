<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    protected $fillable = [
        'slider_image', 'slider_url', 'status',
    ];

    public function get_img()
    {
        return $this->hasOne(Media::class, 'id', 'slider_image');
    }
}
