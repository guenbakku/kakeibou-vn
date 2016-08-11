<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    
    // Những link có thể truy cập mà không cần đăng nhập
    protected $allowable_uris = array(
                                    'user/login',
                                );
    
    public function __construct()
    {   
        parent::__construct();
        
        if (!in_array($this->uri->uri_string(), $this->allowable_uris)){
            if ($this->login_model->isLogin() === false){
                redirect(base_url().Login_model::LOGIN_URL);
            }
        }
    }
    
}
