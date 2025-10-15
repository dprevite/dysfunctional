<?php

use App\Http\Controllers\Api\FunctionController;
use Illuminate\Support\Facades\Route;

Route::apiResource(name: 'functions', controller: FunctionController::class)
    ->only(['index', 'show']);
