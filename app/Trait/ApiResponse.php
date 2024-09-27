<?php

namespace App\Trait;

use Illuminate\Http\JsonResponse;

trait ApiResponse {
    protected function successResponse($message, $data, $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse($message, $code = 400): JsonResponse
    {
        return response()->json(['error' => $message], $code);
    }
}


