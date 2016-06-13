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
            show_error(Constants::ERR_BAD_REQUEST);
        }
        
        if (!empty($post_data = $this->input->post())){
            
            try {
                
                $this->load->library('form_validation');
                
                if ($this->form_validation->run() === false){
                    throw new Exception(validation_errors());
                }
                $this->inout_model->add($type, $post_data);
                $this->flash->success(sprintf(Constants::SUCC_ADD_INOUT_RECORD, $this->inout_model->getCashFlowName($type)));
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
    
    public function edit($id)
    {
        if (!is_numeric($id)){
            show_error(Constants::ERR_BAD_REQUEST);
        }
        
        $ioRecord = $this->inout_model->db->where('iorid', $id)
                                          ->limit(1)
                                          ->get(Inout_model::TABLE)
                                          ->row_array();
        
        if (empty($ioRecord)){
            show_error(Constants::ERR_NOT_FOUND);
        }
        
        if ($ioRecord['cash_flow'] == 'handover'){
            $ioRecord['player'] = $this->inout_model->setPlayersForHandoverEdit($ioRecord);
        }
        
        $_POST = $ioRecord;
        $type = $ioRecord['cash_flow'];
        $view_data             = $ioRecord;
        $view_data['title']    = 'Chỉnh sửa';
        $view_data['form_url'] = base_url()."record/";
        $view_data['type']     = $type;
        $view_data['select']   = array(
            'accounts'   => $this->app_model->getSelectTagData('account_id'),
            'players'    => $this->app_model->getSelectTagData('user_id'),
            'categories' => $this->app_model->getSelectTagData('category_id', $this->inout_model->getInoutTypeCode($type)),
        );
        
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
