<?php

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
