<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MY_Controller {
        
	public function index()
    {   
        redirect(base_url($this->auth->login_url()));
	}
    
    public function edit(string $mode)
    {   
        $method = 'edit_'.$mode;
        if (!is_callable([$this, $method])) {
            show_error(Consts::ERR_BAD_REQUEST);
        }
        call_user_func([$this, 'edit_'.$mode]);
    }
    
    public function edit_password()
    {
        // Do edit password
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            try {
                $this->load->library('form_validation');
                if ($this->form_validation->run() === false){
                    throw new AppException(validation_errors());
                }
                $new_password = $this->input->post('new_password');
                $user_id = $this->auth->user('id');
                $this->user_model->change_password($new_password, $user_id);
                $this->auth->delete_all_other_tokens_of_user($user_id);
                
                $this->flash->success('Thay đổi mật khẩu thành công');
            }
            catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        }
        
        $view_data['title'] = 'Thay đổi mật khẩu';
        $view_data['url'] = [
            'back' => base_url('setting'),
        ];
        $this->template->write_view('MAIN', 'user/edit_password', $view_data);
        $this->template->render();
    }
    
    public function login()
    {
        // Already Login
        if ($this->auth->is_authenticated()){
            return redirect(base_url());
        }
        
        // Do Login
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
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
                    throw new AppException($this->auth->error);
                }
                
                return redirect(base_url());
            }
            catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        }
        
		$this->template->write_view('MAIN', 'user/login');
        $this->template->render();
    }
    
    public function logout()
    {
        $this->auth->destroy();
        redirect(base_url($this->auth->login_url()));
    }
    
    /*
     *--------------------------------------------------------------------
     * Validation rule cho việc kiểm tra password hiện tại có đúng không.
     * Chủ yếu dùng khi muốn thay đổi password.
     *
     * @param   string: password hiện tại
     * @return  boolean
     *--------------------------------------------------------------------
     */
    public function _password_matched(string $old_password): bool
    {   
        $user_id = $this->auth->user('id');
        $this->form_validation->set_message('_password_matched', 'Mật khẩu hiện tại không đúng.');
        return $this->user_model->password_matched($old_password, $user_id);
    }
}
