<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'microsoft',
            function ($app) use ($socialite) {
                $config = $app['config']['services.microsoft'];
                return $socialite->buildProvider(\App\Services\SocialiteProviders\MicrosoftProvider::class, $config);
            }
        );
        $socialite->extend(
            'facebook',
            function ($app) use ($socialite) {
                $config = $app['config']['services.facebook'];
                return $socialite->buildProvider(\App\Services\SocialiteProviders\FacebookProvider::class, $config);
            }
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {        

        Schema::defaultStringLength(191);
                
        $this->app->bind('path.public', function () {
            return base_path('public_html');
        });
    }
}
