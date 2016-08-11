<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
        
	public function index()
    {   
        redirect($this->login_model->getLoginUrl());
	}
    
    public function login()
    {
        // Do Login
        if (!empty($this->input->post())){

            try {
            
                $this->load->library('form_validation');
                
                if ($this->form_validation->run() === false){
                    throw new Exception(validation_errors());
                }
                
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $remember = $this->input->post('remember')==='1'? true : false;
                
                if ($this->login_model->excuteLogin($username, $password, $remember) === false){
                    throw new Exception($this->login_model->getError());
                }
                
                redirect(base_url());
            }
            catch (Exception $e){
                $this->flash->error($e->getMessage());
            }
            
        }
        // Already Login
        elseif ($this->login_model->isLogin()){
            redirect(base_url());
        }
        
		$this->template->write_view('MAIN', 'user/login');
        $this->template->render();
    }
    
    public function logout()
    {
        $this->login_model->delConnection();
        $this->flash->success('Đăng xuất thành công');
        redirect(base_url());
    }
    
}
