<?php

namespace App\Http\Controllers;

use Inertia\Response;

class RuntimesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        return inertia('runtimes');
    }
}
