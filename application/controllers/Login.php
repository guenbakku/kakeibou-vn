<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
    
    private $cookieName = 'loginAuth';
    
	public function index()
    {   
        
        if ($this->login_model->isLogin()){
            redirect(base_url());
        }
        
		$this->template->write_view('MAIN', 'login');
        $this->template->render();
	}
    
    public function doLogin()
    {
        try {
            
            $this->load->library('form_validation');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[32]|xss_clean|htmlspecialchars');
            $this->form_validation->set_rules('password', 'Password', 'required|max_length[32]|xss_clean|htmlspecialchars');
            
            if ($this->form_validation->run() === false){
                throw new Exception(validation_errors());
            }
            
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $remember = $this->input->post('remember')==='1'? true : false;
            
            if ($this->login_model->excuteLogin($username, $password, $remember) === false){
                throw new Exception($this->login_model->getErr());
            }
            
            redirect(base_url());
            
        }
        catch (Exception $e){
            
            show_error($e->getMessage());
            
        }
    }
    
    public function doLogout()
    {
        $this->login_model->delLoginConn();
        redirect(base_url().'login');
    }
    
}
