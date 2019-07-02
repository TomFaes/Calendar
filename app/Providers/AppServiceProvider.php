<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        $this->app->bind('App\Repositories\Contracts\IUser', 'App\Repositories\UserRepo');
        $this->app->bind('App\Repositories\Contracts\IGroup', 'App\Repositories\GroupRepo');
        $this->app->bind('App\Repositories\Contracts\ISeason', 'App\Repositories\SeasonRepo');
        $this->app->bind('App\Repositories\Contracts\IAbsence', 'App\Repositories\AbsenceRepo');
        $this->app->bind('App\Repositories\Contracts\ITeam', 'App\Repositories\TeamRepo');
        
        $this->app->bind('path.public', function()
        {
            return base_path('public_html');
        });
    }
}
