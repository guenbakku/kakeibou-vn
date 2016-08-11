<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends MY_Controller {
    
	public function index()
    {   
        d($this->uri->uri_string());
	}
    
}
