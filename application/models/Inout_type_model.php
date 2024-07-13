<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inout_type_model extends App_Model
{
    public const TABLE = 'inout_types';

    // Tên của loại thu chi
    public static $INOUT_TYPE = [
        1 => 'Thu',
        2 => 'Chi',
    ];
}
