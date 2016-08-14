<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    
    protected $ctrl_base_url = '';
    
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
    
    public function base_url(){
        $base       = $this->ctrl_base_url;
        $base       = empty($base)? '' : rtrim($base, '/').'/';
        $class_name = get_class($this);
        return base_url().strtolower($base.$class_name).'/';
    }
}
