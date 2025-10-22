<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FunctionNotFoundException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error'   => 'Function not found',
            'message' => $this->getMessage(),
        ], 404);
    }
}
