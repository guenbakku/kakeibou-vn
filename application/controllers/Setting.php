<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends MY_Controller {
        
	public function index()
    {   
        $view_data['title'] = 'Thay đổi thiết đặt';
        $view_data['url'] = [
            'back'  => base_url(),
        ];
        $this->template->write_view('MAIN', 'setting/menu', $view_data);
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
            return redirect($this->referer->getSession());
        }
        else {
            // Lưu referer của page access đến form 
            $this->referer->saveSession();
        }
        
        $view_data['setting'] = current($data);
        $view_data['title'] = $view_data['setting']['name'];
        $view_data['url'] = [
            'form'  => $this->base_url([__FUNCTION__, $item]),
            'back'  => $this->base_url(),
        ];
        $this->template->write_view('MAIN', 'setting/form', $view_data);
        $this->template->render();
    }
}
