<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;

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
        DB::listen(function ($query) {
            Log::info('SQL Query Executed', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ]);
        });

        // Custom Blade directives for system access
        Blade::if('hasAccess', function ($module) {
            return auth()->check() && auth()->user()->hasAccess($module);
        });

        Blade::if('hasAnyAccess', function ($modules) {
            return auth()->check() && auth()->user()->hasAnyAccess($modules);
        });

        Blade::if('canAccessSpk', function () {
            return auth()->check() && auth()->user()->hasAccess('spk_garage');
        });

        Blade::if('canAccessPr', function () {
            return auth()->check() && auth()->user()->hasAccess('pr');
        });
    }
}
