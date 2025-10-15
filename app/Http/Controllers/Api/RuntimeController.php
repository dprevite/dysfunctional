<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Config;
use Illuminate\Http\JsonResponse;

class RuntimeController extends Controller
{
    public function __construct(
        protected Config $config
    ) {}

    /**
     * Display a listing of all runtimes.
     */
    public function index(): JsonResponse
    {
        $runtimes = $this->config->runtimes();

        return response()->json([
            'data' => $runtimes,
        ]);
    }

    /**
     * Display the specified runtime.
     */
    public function show(string $language): JsonResponse
    {
        $runtimes = $this->config->runtimes();

        $runtime = collect($runtimes)->firstWhere('language', $language);

        if (! $runtime) {
            return response()->json([
                'message' => 'Runtime not found',
            ], 404);
        }

        return response()->json([
            'data' => $runtime,
        ]);
    }
}
