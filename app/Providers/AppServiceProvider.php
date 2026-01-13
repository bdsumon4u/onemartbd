<?php

namespace App\Providers;

use App\Models\WebSettings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    #[\Override]
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', function ($view): void {
            $view->with('web_settings', cache()->rememberForever('web_settings', function() {
                return WebSettings::with('get_logo:id,file_url', 'get_fav:id,file_url')->find(1);
            }));
        });
    }
}
