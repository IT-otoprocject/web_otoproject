<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;

class ProductionSafetyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Block dangerous commands in production
        if (config('app.env') === 'production') {
            
            // Override dangerous migrate commands
            $this->app->bind('command.migrate.fresh', function () {
                throw new \Exception('migrate:fresh is BLOCKED in production for safety!');
            });
            
            $this->app->bind('command.migrate.reset', function () {
                throw new \Exception('migrate:reset is BLOCKED in production for safety!');
            });
            
            $this->app->bind('command.migrate.refresh', function () {
                throw new \Exception('migrate:refresh is BLOCKED in production for safety!');
            });
            
            $this->app->bind('command.db.wipe', function () {
                throw new \Exception('db:wipe is BLOCKED in production for safety!');
            });
        }
    }
}
