<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider; 
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use App\Services\LinkedInOpenIDProvider;

class SocialiteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->make(SocialiteFactory::class)->extend('linkedin-oidc', function ($app) {
            $config = $app['config']['services.linkedin'];

            return new LinkedInOpenIDProvider(
                $app['request'],
                $config['client_id'],
                $config['client_secret'],
                $config['redirect']
            );
        });
    }
}
