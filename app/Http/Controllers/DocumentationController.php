<?php

namespace App\Http\Controllers;

use Inertia\Response;

class DocumentationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): Response
    {
        return inertia('documentation');
    }
}
