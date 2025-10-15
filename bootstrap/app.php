<?php

use App\Http\Middleware\HandleAppearance;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        using: function () {
            loadRoutesFromDirectory(__DIR__ . '/../routes/web', 'web');
            loadRoutesFromDirectory(__DIR__ . '/../routes/api', 'api');
        },
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(append: [
            HandleAppearance::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

function loadRoutesFromDirectory(string $path, string $middleware): void
{
    if (is_dir($path)) {
        foreach (glob("{$path}/*.php") as $routeFile) {
            Route::middleware($middleware)->group($routeFile);
        }
    } elseif (file_exists("{$path}.php")) {
        Route::middleware($middleware)->group("{$path}.php");
    }
}
