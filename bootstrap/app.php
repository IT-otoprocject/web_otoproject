<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Register route model bindings for Document Management
            Route::model('document', \App\Models\DocumentManagement\Document::class);
            Route::model('folder', \App\Models\DocumentManagement\DocumentFolder::class);
            
            // Custom binding for folder by slug
            Route::bind('folder', function ($value) {
                // Try to find by ID first (for routes using {folder} with ID)
                if (is_numeric($value)) {
                    return \App\Models\DocumentManagement\DocumentFolder::findOrFail($value);
                }
                // Otherwise, find by slug
                return \App\Models\DocumentManagement\DocumentFolder::where('slug', $value)
                    ->where('is_active', true)
                    ->firstOrFail();
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'level' => \App\Http\Middleware\CheckUserLevel::class,
            'system_access' => \App\Http\Middleware\CheckSystemAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
