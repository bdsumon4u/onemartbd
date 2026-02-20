<?php

namespace App\Services;

use App\Models\WebSettings;

class MetaPixel extends \Combindma\FacebookPixel\MetaPixel
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        parent::__construct();

        $settings = WebSettings::query()->first([
            'fb_pixel_id', 'fb_test_event_code', 'fb_cpi_access_token',
        ]);

        if (! $settings) {
            return;
        }

        if ($settings->fb_pixel_id) {
            $this->setPixelId($settings->fb_pixel_id);
        }

        if ($settings->fb_test_event_code) {
            $this->setTestEventCode($settings->fb_test_event_code);
        }

        if ($settings->fb_cpi_access_token) {
            $this->setToken($settings->fb_cpi_access_token);
        }

        if ($this->pixelId()) {
            $this->enable();
        }
    }
}
