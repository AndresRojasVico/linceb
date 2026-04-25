<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware(['web', 'auth'])
                ->prefix('tasks')
                ->group(base_path('routes/task.php'));

            Route::middleware(['web', 'auth'])
                ->prefix('projects')
                ->group(base_path('routes/projects.php'));

            Route::middleware(['web', 'auth', 'superadmin'])
                ->prefix('sadmin')
                ->group(base_path('routes/sadmin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registramos el alias aquí:
        $middleware->alias([
            'superadmin' => \App\Http\Middleware\EnsureUserIsSuperAdmin::class,
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
