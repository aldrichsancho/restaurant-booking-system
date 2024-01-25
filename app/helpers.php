<?php

use Illuminate\Http\JsonResponse;

if (! function_exists('commonResponse')) {
    function commonResponse(
        int $status, string $message, array $additional_data = []
    ): JsonResponse
    {
        $data = [
            'status' => $status,
            'message' => $message
        ];
        if (count($additional_data) > 0) {
            $data = array_merge($data, $additional_data);
        }

        return response()->json($data, $status);
    }
}
