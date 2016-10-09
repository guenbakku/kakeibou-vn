<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account_model extends App_Model {
    
    const TABLE = 'accounts';
    
    // Icon cho các loại tài khoản (tiền mặt và tài khoản ngân hàng)
    public static $ICONS = array(
        1 => 'fa-money',
        2 => 'fa-bank',
        3 => 'fa-bank',
        4 => 'fa-bank',
        5 => 'fa-bank',
        6 => 'fa-bank',
        7 => 'fa-bank',
        8 => 'fa-bank',
        9 => 'fa-bank',
    );
    
}