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
        
        $this->output->set_header('Access-Control-Allow-Origin: '.base_url());
        $this->output->remove_headers(array('X-Powered-By'));
        
        // Nếu chưa đăng nhập thì chuyển về trang login
        if (!in_array($this->uri->uri_string(), $this->allowable_uris)){
            if (!$this->login_model->isLogin()){
                redirect(base_url().Login_model::LOGIN_URL);
            }
        }
    }
    
    /*
     *--------------------------------------------------------------------
     * Tạo base_url có tự động thêm tên controller class
     * Ngoài ra nếu $ctr_base_url được chỉ định, sẽ thêm phần này vào trước 
     * tên controller class.
     *--------------------------------------------------------------------
     */
    public function base_url($uris=null, $protocol=null)
    {
        $base       = strtolower($this->ctrl_base_url);
        $base       = empty($base)? '' : rtrim($base, '/').'/';
        $class_name = strtolower(get_class($this));
        
        if(is_array($uris)){
            $uris = implode('/', $uris);
        }
        $uris = $base.$class_name . '/' . $uris;
        
        return base_url($uris, $protocol);
    }
}
