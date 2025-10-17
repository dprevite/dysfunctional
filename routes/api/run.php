<?php

declare(strict_types=1);

use App\Http\Controllers\Api\RunController;
use App\Http\Middleware\RunMiddleware;

Route::prefix('run')
    ->middleware([RunMiddleware::class])
    ->group(function () {
        Route::any(
            uri: '{any?}',
            action: [RunController::class, 'handle']
        )
            ->where('any', '.*');
    });
