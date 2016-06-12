<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inout extends CI_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        if ($this->login_model->isLogin() === false){
            redirect(base_url().'login');
        }
    }    
    
    public function index()
    {
        $this->template->write_view('MAIN', 'inout/menu');
        $this->template->render();
    }
    
	public function add($type)
    {   
        
        if (! $cashFlowName = $this->inout_model->getCashFlowName($type)){
            show_404();
        }
        
        if (!empty($post_data = $this->input->post())){
            
            try {
                
                $this->load->library('form_validation');
                
                if ($this->form_validation->run() === false){
                    throw new Exception(validation_errors());
                }
                $this->inout_model->add($type, $post_data);
                $this->flash->success(sprintf('Thêm dữ liệu <strong>%s</strong> thành công', $this->inout_model->getCashFlowName($type)));
                redirect(base_url());
                
            }
            catch (Exception $e) {
                $this->flash->error($e->getMessage());
            }
            
        }
        
        $view_data['type']     = $type;
        $view_data['title']    = $cashFlowName;
        $view_data['form_url'] = base_url().$this->uri->uri_string();
        $view_data['select']   = array(
            'accounts'   => $this->app_model->getSelectTagData('account_id'),
            'players'    => $this->app_model->getSelectTagData('user_id'),
            'categories' => $this->app_model->getSelectTagData('category_id', $this->inout_model->getInoutTypeCode($type)),
        );
        
		$this->template->write_view('MAIN', 'inout/form', $view_data);
        $this->template->render();
	}
    
    public function edit($num)
    {
        $view_data['title']    = 'Chỉnh sửa';
        $view_data['form_url'] = base_url()."record/";
        
		$this->template->write_view('MAIN', 'inout/form', $view_data);
        $this->template->render();
    }
    
    public function summary()
    {   
		$this->template->write_view('MAIN', 'inout/summary');
        $this->template->render();
	}
    
    public function detail()
    {
		$this->template->write_view('MAIN', 'inout/detail');
        $this->template->render();
    }
    
}
