<?php

use App\Http\Controllers\Admin\ShowCreateFunctionPage;
use App\Http\Controllers\Admin\StoreFunction;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\FunctionsController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\RuntimesController;
use App\Http\Controllers\ShowLogController;
use Illuminate\Support\Facades\Route;

// Home page
Route::get(
    uri: '/',
    action: function () {
        return inertia('welcome');
    })
    ->name('home');

Route::middleware(['auth', 'verified'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/functions', FunctionsController::class)
            ->name('functions');

        Route::get('/runtimes', RuntimesController::class)
            ->name('runtimes');

        Route::get('/analytics', AnalyticsController::class)
            ->name('analytics');

        Route::get('/logs', LogsController::class)
            ->name('logs');

        Route::get('/logs/{logId}', ShowLogController::class)
            ->name('logs.show');

        Route::get('/documentation', DocumentationController::class)
            ->name('documentation');

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
