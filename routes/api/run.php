<?php

declare(strict_types=1);

use App\Http\Controllers\Api\RunController;

Route::prefix('run')
    ->group(function () {
        Route::post(
            uri: '/test',
            action: [RunController::class, 'handle']
        );
    });
