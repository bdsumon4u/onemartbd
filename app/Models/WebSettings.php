<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WebSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_address', 'website_phone', 'website_phone2', 'website_phone3', 'website_email', 'website_email2',
        'website_facebook', 'website_twitter', 'website_instagram', 'website_youtube', 'website_header_logo',
        'website_favicon', 'website_copyright_text', 'currency_sign', 'bkash_merchant_numb', 'fb_pixel',
        'is_order_confirm_sms', 'order_confirm_sms', 'order_custom_sms', 'api_access_token', 'fb_pixel_id',
        'fb_cpi_access_token', 'whatsapp_number', 'gtm_script_head', 'gtm_script_body',
        'primary_color', 'secondary_color', 'header_top_color', 'header_color', 'header_bottom_color',
        'button_color', 'button_hover_color', 'wp_phone_number_id', 'wp_access_token',
    ];

    public function logo(): HasOne
    {
        return $this->hasOne(Media::class, 'id', 'website_header_logo');
    }

    public function favicon(): HasOne
    {
        return $this->hasOne(Media::class, 'id', 'website_favicon');
    }

    // Backward-compatible accessors
    public function get_logo(): HasOne
    {
        return $this->logo();
    }

    public function get_fav(): HasOne
    {
        return $this->favicon();
    }
}
