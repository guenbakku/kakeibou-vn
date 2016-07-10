<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function currency($num, $plus_sign=true){
    
    if ($plus_sign === true){
        $sign = $num > 0? '+' : '';
    }
    else {
        $sign = '';
    }
    
    return $sign.number_format($num);
}