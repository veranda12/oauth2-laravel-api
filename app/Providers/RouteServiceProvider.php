<?php

namespace App\Providers;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Http\Middleware\TokenFromCookie;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
use Log;
class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Define rate limiter
        Log::info("cek route");
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
        Log::info("cek route2");
        Route::middleware(TokenFromCookie::class);
        // Optionally, you can add more route-specific bootstraps here
    }
}
