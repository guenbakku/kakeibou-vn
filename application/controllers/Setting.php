<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends MY_Controller {
        
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
            return redirect($this->referer->getSession());
        }
        else {
            // Lưu referer của page access đến form 
            $this->referer->saveSession();
        }
        
        $view_data['setting'] = current($data);
        $view_data['url'] = array(
            'form'  => $this->base_url(array(__FUNCTION__, $item)),
        );
        $this->template->write_view('MAIN', 'setting/form', $view_data);
        $this->template->render();
    }
}
