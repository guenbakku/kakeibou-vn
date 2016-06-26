<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function currency($num){
    $plus_sign = $num > 0? '+' : '';
    return $plus_sign.number_format($num);
}