<?php

use App\Http\Controllers\Admin\ShowCreateFunctionPage;
use App\Http\Controllers\Admin\StoreFunction;
use Illuminate\Support\Facades\Route;

// This is a test, function routes will be automatically generated based
// on YAML.
Route::get(
    uri: '/test',
    action: function () {
        return shell_exec('docker run --rm dysfunctional-php-8.4');
    });

// Home page
Route::get(
    uri: '/',
    action: function () {
        return inertia('welcome');
    })
    ->name('home');

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/dashboard', function () {
            return inertia('dashboard');
        })
            ->name('dashboard');

        // Application
        Route::prefix('admin')
            ->name('admin.')
            ->group(function () {
                Route::get(
                    uri: '/functions/create',
                    action: ShowCreateFunctionPage::class)
                    ->name('functions.create');

                Route::post(
                    uri: '/functions/create',
                    action: StoreFunction::class)
                    ->name('functions.store');
            });
    });

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
