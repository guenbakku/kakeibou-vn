<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        if ($this->login_model->isLogin() === false){
            redirect(base_url().Login_model::LOGIN_URL);
        }
        
        $this->load->model('search_model');
    }
    
	public function index()
    {   
        $result = null;
        if (!empty($this->input->get()))
        {
            try {
                $_POST = $this->input->get();
                
                $condition_keys = array('amount', 'player', 'inout_type', 'memo', 'from', 'to');
                $is_all_fields_empty = true;
                
                foreach ($condition_keys as $key){
                    if (!empty($this->input->get($key))){
                        $is_all_fields_empty = false;
                        $this->search_model->$key = $this->input->get($key);
                    }
                    else {
                        $this->search_model->$key = null;
                    }
                }
                
                if ($is_all_fields_empty){
                    throw new Exception('Chưa nhập điều kiện tìm kiếm');
                }
                
                $result = $this->search_model->search();
            }
            catch (Exception $e) {
                $this->flash->error($e->getMessage());
            }
        }
        
        $view_data['list']        = $result;
        $view_data['total_items'] = count($view_data['list']);
        $view_data['form_url']    = base_url().strtolower(__CLASS__);
        $view_data['title']       = 'Tìm kiếm chi tiêu';
        $view_data['show_form']   = $result===null? true : false;
        $view_data['select']      = array(
            'players'     => array(0=> 'Tất cả') + $this->app_model->getSelectTagData('user_id'),
            'inout_types' => array(0=> 'Tất cả') + $this->app_model->getSelectTagData('inout_type_id'),
        );
		$this->template->write_view('MAIN', 'search/search', $view_data);
        $this->template->render();
	}
    
}
