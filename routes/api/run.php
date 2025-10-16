<?php

declare(strict_types=1);

use App\Http\Controllers\Api\RunController;

Route::prefix('run')
    ->group(function () {
        Route::any(
            uri: '{any?}',
            action: [RunController::class, 'handle']
        )->where('any', '.*');
    });
