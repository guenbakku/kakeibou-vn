<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('currency')) {
    function currency($num, bool $plus_sign = true)
    {
        if ($plus_sign === true) {
            $sign = $num > 0 ? '+' : '';
        } else {
            $sign = '';
        }

        return $sign.number_format($num);
    }
}
