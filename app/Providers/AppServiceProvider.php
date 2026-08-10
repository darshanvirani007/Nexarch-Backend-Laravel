<?php

namespace App\Providers;

use App\Database\SupabasePostgresConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Connection;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Connection::resolverFor('pgsql', fn ($connection, $database, $prefix, $config) =>
            new SupabasePostgresConnection($connection, $database, $prefix, $config)
        );

        Model::preventLazyLoading(! app()->isProduction());
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->attributes->get('user_id') ?: $request->ip()));
    }
}
