<?php

if (!function_exists('sendResponse')) {
    function sendResponse($data, $error = null, $status = 200, $params = null): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'data' => $data,
            'error' => $error,
            'params' => $params
        ], $status);
    }
}

if (!function_exists('isEven')) {
    function isEven($num): bool
    {
        if (($num % 2) == 0) {
            return true;
        } else {
            return false;
        }
    }
}
