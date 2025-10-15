<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Config;
use Illuminate\Http\JsonResponse;

class FunctionController extends Controller
{
    public function __construct(
        protected Config $config
    ) {}

    /**
     * Display a listing of all functions.
     */
    public function index(): JsonResponse
    {
        $functions = $this->config->functions();

        return response()->json([
            'data' => $functions,
        ]);
    }

    /**
     * Display the specified function.
     */
    public function show(string $name): JsonResponse
    {
        $functions = $this->config->functions();

        $function = collect($functions)->firstWhere('name', $name);

        if (! $function) {
            return response()->json([
                'message' => 'Function not found',
            ], 404);
        }

        return response()->json([
            'data' => $function,
        ]);
    }
}
