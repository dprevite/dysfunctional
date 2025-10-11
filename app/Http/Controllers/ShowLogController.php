<?php

namespace App\Http\Controllers;

use Inertia\Response;

class ShowLogController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(string $logId): Response
    {
        return inertia('log-detail', [
            'logId' => $logId,
        ]);
    }
}
