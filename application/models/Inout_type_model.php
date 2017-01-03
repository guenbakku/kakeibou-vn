<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inout_type_model extends App_Model {
    
    const TABLE = 'inout_types';
    
    // Tên của loại thu chi
    public static $INOUT_TYPE = array(
        1 => 'Thu',
        2 => 'Chi',
    );
}