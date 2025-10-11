<?php

namespace App\Http\Controllers;

use Inertia\Response;

class AnalyticsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        return inertia('analytics');
    }
}
