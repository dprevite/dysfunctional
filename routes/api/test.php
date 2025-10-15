<?php

declare(strict_types=1);

Route::prefix('run')
    ->group(function () {
        Route::post(
            uri: '/test',
            action: function (): string {

            });
    });
