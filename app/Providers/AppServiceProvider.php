<?php

namespace App\Providers;

use App\WebSettings;
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
            $web_config = cache()->rememberForever('web_settings', fn () => $web_config = WebSettings::with('get_logo:id,file_url', 'get_fav:id,file_url')->find(1));

            $view->with('web_settings', $web_config);
        });
    }
}
