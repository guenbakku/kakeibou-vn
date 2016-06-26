<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        if ($this->login_model->isLogin() === false){
            redirect(base_url().Login_model::LOGIN_URL);
        }
    }
    
	public function index()
    {   
        $this->template->write_view('MAIN', 'setting/menu');
        $this->template->render();
	}
    
    public function edit($item)
    {
        if (empty($data = $this->setting_model->get($item))){
            show_error(Constants::ERR_BAD_REQUEST);
        }
        
        if (!empty($this->input->post())){
                
            $this->setting_model->edit($this->input->post());
            
            $this->flash->success(Constants::SUCC_EDIT_SETTING);
            
            // Redirect tới danh sách detail (được ghi trong $_GET['goto'])
            $goto = base64_decode($this->input->get('goto'));
            if ($goto == null) $goto = base_url();
            redirect($goto);
            exit();
        }
        
        $view_data['setting'] = current($data);
        $this->template->write_view('MAIN', 'setting/form', $view_data);
        $this->template->render();
    }
    
}
