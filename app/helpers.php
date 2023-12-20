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

if (!function_exists('calcularDescuento')) {
    function calcularDescuento(float $total, float $coef, int $decimals = 2): float
    {
        $subTotal = round($total * 0.8 * $coef);
        $total = number_format($subTotal, $decimals);

        return (float) $total;
    }
}

if (!function_exists('get_total_price')) {
    function get_total_price($detail)
    {
        $total = 0;
        foreach ($detail as $item) {
            $total += $item['amount'] * $item['unit_price'];
        }

        return $total;
    }
}
