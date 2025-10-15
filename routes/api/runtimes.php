<?php

use App\Http\Controllers\Api\RuntimeController;
use Illuminate\Support\Facades\Route;

Route::apiResource(name: 'runtimes', controller: RuntimeController::class)
    ->only(['index', 'show']);
