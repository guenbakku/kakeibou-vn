<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        if ($this->login_model->isLogin() === false){
            redirect(base_url().Login_model::LOGIN_URL);
        }
    }
    
	public function index()
    {           
		$this->template->write_view('MAIN', 'home');
        $this->template->render();
	}
    
}
