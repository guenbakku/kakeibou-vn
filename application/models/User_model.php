<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends App_Model {
    
    const TABLE = 'users';
    
    protected $columnNamesforSelectTagMethod = array('id', 'fullname');
    
}