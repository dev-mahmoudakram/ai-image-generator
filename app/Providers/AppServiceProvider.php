<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Paginator::defaultView('pagination.custom');

        $caBundle = config('services.http.ca_bundle');

        if (is_string($caBundle) && $caBundle !== '' && is_file($caBundle)) {
            Http::globalOptions(['verify' => $caBundle]);
        }

        RateLimiter::for('public.generate', function (Request $request) {
            return Limit::perHour(config('ai.generate_per_hour', 5))
                ->by($request->ip());
        });
    }
}
