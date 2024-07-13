<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

function currency($num, bool $plus_sign = true)
{
    if (true === $plus_sign) {
        $sign = $num > 0 ? '+' : '';
    } else {
        $sign = '';
    }

    return $sign.number_format($num);
}
