<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends MY_Controller {
    
    public $ctrl_base_url = 'setting/category/';
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
    }
    
	public function index()
    {   
        if(!empty($this->input->post()))
        {
            $this->category_model->editOrderNo($this->input->post('categories'));
            
            // Redirect tới page được ghi trong $_GET['goto']
            $goto = base64_decode($this->input->get('goto'));
            if ($goto == null) $goto = base_url();
            redirect($goto);
            return;
        }
        
        $inout_type_id = (int)$this->input->get('inout_type_id');
        if(!in_array($inout_type_id, array(1,2))){
            $inout_type_id = 1;
        }
        
        // Lấy link HTTP_REFERER
        $http_referer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : null;
        
        $view_data['categories'] = $this->category_model->get(null, array('inout_type_id' => $inout_type_id));
        $view_data['form_url'] = base_url().$this->uri->uri_string().'?goto='.base64_encode($http_referer);
        $this->template->write_view('MAIN', 'category/home', $view_data);
        $this->template->render();
	}
    
    public function add()
    {
        if (!empty($this->input->post())){
            
            try {
                
                $this->load->library('form_validation');
                
                if ($this->form_validation->run() === false){
                    throw new Exception(validation_errors());
                }
                
                $this->category_model->add($this->input->post());
                
                $this->flash->success(Constants::SUCC_ADD_CATEGORY);
                // Redirect tới page được ghi trong $_GET['goto']
                $goto = base64_decode($this->input->get('goto'));
                if ($goto == null) $goto = base_url();
                redirect($goto);
                return;
            }
            catch (Exception $e) {
                $this->flash->error($e->getMessage());
            }
            
        }
        
        // Lấy link HTTP_REFERER
        $http_referer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : null;
        
        $view_data['form_url'] = $this->base_url().'add/?goto='.base64_encode($http_referer);
        $view_data['title']    = 'Thêm danh mục';
        $view_data['select']   = array(
            'inout_types' => $this->app_model->getSelectTagData('inout_type_id'),
        );
        
        $this->template->write_view('MAIN', 'category/form', $view_data);
        $this->template->render();
    }
    
    public function edit($id=null)
    {
        try
        {
            if (!is_numeric($id)){
                throw new Exception(Constants::ERR_BAD_REQUEST);
            }
            
            if (!empty($this->input->post())){
                
                try
                {
                    $this->load->library('form_validation');
                
                    if ($this->form_validation->run() === false){
                        throw new Exception(validation_errors());
                    }
                    
                    $this->category_model->edit($id, $this->input->post());
                    $this->flash->success(Constants::SUCC_EDIT_CATEGORY);
                    
                    // Redirect tới page được ghi trong $_GET['goto']
                    $goto = base64_decode($this->input->get('goto'));
                    if ($goto == null) $goto = base_url();
                    redirect($goto);
                    return;
                }
                catch (Exception $e)
                {
                    $this->flash->error($e->getMessage());
                }
            }
            else {
                $category_data = $this->category_model->get($id);
                
                if (empty($category_data))
                {
                    throw new Exception(Constants::ERR_NOT_FOUND);
                }
                $_POST = $category_data;
            }
            
            // Lấy link HTTP_REFERER
            $http_referer = isset($_SERVER['HTTP_REFERER'])? $_SERVER['HTTP_REFERER'] : null;

            $view_data['form_url'] = $this->base_url().'edit/'.$id.'?goto='.base64_encode($http_referer);
            $view_data['del_url']  = $this->base_url().'del/'.$id.'?goto='.base64_encode($http_referer);
            $view_data['title']    = 'Sửa danh mục';
            $view_data['select']   = array(
                'inout_types' => $this->app_model->getSelectTagData('inout_type_id'),
            );
            
            $this->template->write_view('MAIN', 'category/form', $view_data);
            $this->template->render();
        }
        catch (Exception $e)
        {
            show_error($e->getMessage());
        }
    }
    
    public function del($id)
    {
        try
        {
            if (!is_numeric($id)){
                throw new Exception(Constants::ERR_BAD_REQUEST);
            }
            
            $this->category_model->del($id);
            $this->flash->success(Constants::SUCC_DEL_CATEGORY);
            
            // Redirect tới page được ghi trong $_GET['goto']
            $goto = base64_decode($this->input->get('goto'));
            if ($goto == null) $goto = base_url();
            redirect($goto);
            return;
        }
        catch (Exception $e)
        {
            $this->flash->error($e->getMessage());
            redirect(base_url(). strtolower(__CLASS__ . '/edit/' . $id));
        }
    }
    
}
