<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {
        
	public function index()
    {   
        redirect(base_url($this->auth->login_url()));
	}
    
    public function login()
    {
        // Already Login
        if ($this->auth->is_authenticated()){
            return redirect(base_url());
        }
        
        // Do Login
        if (!empty($this->input->post())){
            try {
            
                $this->load->library('form_validation');
                
                if ($this->form_validation->run() === false){
                    throw new Exception(validation_errors());
                }
                
                $auth_info = [
                    'username' => $this->input->post('username'),
                    'password' => $this->input->post('password'),
                    'remember' => $this->input->post('remember')==='1'? true : false,
                ];
                if ($this->auth->auth_info($auth_info)->authenticate() === false){
                    throw new Exception($this->auth->error);
                }
                
                return redirect(base_url());
            }
            catch (Exception $e){
                $this->flash->error($e->getMessage());
            }
            
        }
        
		$this->template->write_view('MAIN', 'user/login');
        $this->template->render();
    }
    
    public function logout()
    {
        $this->auth->logout();
        redirect(base_url($this->auth->login_url()));
    }
}
