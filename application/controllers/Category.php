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
            $this->flash->success(Constants::SUCC_EDIT_CATEGORY_ORDER);
            return redirect($this->referer->get());
        }
        
        $inout_type_id = (int)$this->input->get('inout_type_id');
        if(!in_array($inout_type_id, array(1, 2))){
            $inout_type_id = 1;
        }
        
        $view_data['categories'] = $this->category_model->get(null, array('inout_type_id' => $inout_type_id));
        $view_data['form_url'] = base_url().$this->uri->uri_string();
        $view_data['inout_type_id'] = $inout_type_id;
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
                return redirect($this->referer->getSession());
            }
            catch (Exception $e) {
                $this->flash->error($e->getMessage());
            }
        }
        else {
            // Lưu referer của page access đến form
            $this->referer->saveSession();
        }
        
        $_POST['inout_type_id'] = $this->input->get('inout_type_id');
        
        $view_data['form_url']      = $this->base_url().'add/';
        $view_data['title']         = 'Thêm danh mục';
        $view_data['select']   = array(
            'inout_types' => $this->inout_type_model->getSelectTagData(),
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
                    return redirect($this->referer->getSession());
                }
                catch (Exception $e)
                {
                    $this->flash->error($e->getMessage());
                }
            }
            else {
                $category_data = $this->category_model->get($id);
                
                if (empty($category_data)){
                    throw new Exception(Constants::ERR_NOT_FOUND);
                }
                $_POST = $category_data;
                
                // Lưu referer của page access đến form
                $this->referer->saveSession();
            }

            $view_data['form_url'] = $this->base_url().'edit/'.$id;
            $view_data['del_url']  = $this->base_url().'del/'.$id;
            $view_data['title']    = 'Sửa danh mục';
            $view_data['select']   = array(
                'inout_types' => $this->inout_type_model->getSelectTagData(),
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
        }
        catch (Exception $e)
        {
            $this->flash->error($e->getMessage());
        }
        
        return redirect($this->referer->getSession());
    }
    
}
