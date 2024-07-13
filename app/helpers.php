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

if (!function_exists('redondearNumero')) {
    function redondearNumero($numero)
    {
        $residuo = $numero % 100;
        if ($residuo >= 50) {
            return (int) ($numero + (100 - $residuo));
        } else {
            return (int) $numero - $residuo;
        }
    }
}

if (!function_exists('formatoMoneda')) {
    function formatoMoneda($number, $decimals = 0, $dec_point = ',', $thousands_sep = '.')
    {
        if (!$number) {
            return '$ ' . number_format(0, $decimals, $dec_point, $thousands_sep);
        }
        return '$ ' . number_format($number, $decimals, $dec_point, $thousands_sep);
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
    function get_total_price($detail, $coef = 1)
    {
        $total = 0;
        foreach ($detail as $item) {
            $total += $item['amount'] * $item['unit_price'] * $coef;
        }

        return $total;
    }
}

if (!function_exists('truncateString')) {
    function truncateString($string, $int)
    {
        // Verifica si la longitud de la cadena es mayor a 50
        if (strlen($string) > $int) {
            // Si es mayor, devuelve solo los primeros 50 caracteres
            return substr($string, 0, $int) . '...';
        }
        // Si no es mayor, devuelve la cadena original
        return $string;
    }
}
