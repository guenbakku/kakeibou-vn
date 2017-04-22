<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends MY_Controller {
    
    protected $ctrl_base_url = 'setting';
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
    }
    
	public function index()
    {   
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            $data = $this->input->post('categories');
            $this->category_model->edit_batch($data, 'id');
            $this->flash->success(Consts::SUCC_EDIT_CATEGORY_ORDER);
            return redirect($this->referer->get());
        }
        
        $inout_type_id = (int)$this->input->get('inout_type_id');
        if (!in_array($inout_type_id, array(1, 2))) {
            $inout_type_id = 1;
        }
        
        $view_data['title'] = 'Quản lý danh mục';
        $view_data['categories']    = $this->category_model->get(null, ['inout_type_id' => $inout_type_id]);
        $view_data['inout_type_id'] = $inout_type_id;
        $view_data['url'] = [
            'form'   => $this->base_url(),
            'subNav' => [
                $this->base_url().'?inout_type_id=1',
                $this->base_url().'?inout_type_id=2',
            ],
            'add'    => $this->base_url(['add', '?inout_type_id=']).$inout_type_id,
            'edit'   => $this->base_url(['edit', '%s']),
            'back'   => base_url('setting'),
        ];
        $this->template->write_view('MAIN', 'category/home', $view_data);
        $this->template->render();
	}
    
    public function month_estimated_outgo()
    {   
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            try {
                $this->load->library('form_validation');
                $data = $this->input->post('categories');
                
                // Validate form
                foreach($data as $i => $category) {
                    $this->form_validation->set_rules(
                        sprintf('categories[%d][month_estimated_inout]', $i),
                        sprintf('Dự định chi tháng này của %s', $category['name']),
                        'required|trim|greater_than_equal_to[0]'
                    );
                }
                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }
                
                $this->category_model->edit_batch($data, 'id');
                $this->flash->success(Consts::SUCC_EDIT_MONTH_ESTIMATED_OUTGO);
                return redirect($this->referer->get());
            }
            catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        }
        
        $inout_type_id = array_flip($this->inout_model::$INOUT_TYPE)['Chi'];
        $view_data['categories'] = $this->category_model->get(null, ['inout_type_id' => $inout_type_id]);
        $view_data['title'] = 'Dự định chi tháng này';
        $view_data['url'] = [
            'form'      => $this->base_url([__FUNCTION__]),
            'back'      => base_url('setting'),
        ];
        $_POST['categories'] = $view_data['categories'];
        $this->template->write_view('MAIN', 'category/month_estimated_outgo', $view_data);
        $this->template->render();
    }
    
    public function add()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            try {
                $this->load->library('form_validation');
                
                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }
                
                $this->category_model->add($this->input->post());
                $this->flash->success(Consts::SUCC_ADD_CATEGORY);
                return redirect($this->referer->getSession());
            }
            catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        }
        else {
            // Lưu referer của page access đến form
            $this->referer->saveSession();
            $_POST['inout_type_id'] = $this->input->get('inout_type_id');
        }
        
        $view_data['title'] = 'Thêm danh mục';
        $view_data['select'] = [
            'inout_types' => $this->inout_type_model->get_select_tag_data(),
        ];
        $view_data['url'] = [
            'form' => $this->base_url(__FUNCTION__),
            'back' => $this->referer->getSession(null, false),
        ];
        
        $this->template->write_view('MAIN', 'category/form', $view_data);
        $this->template->render();
    }
    
    public function edit($id=null)
    {
        if (!is_numeric($id)) {
            show_error(Consts::ERR_BAD_REQUEST);
        }
        
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // Chuyển sang xử lý xóa category nếu lựa chọn xóa
            if ((bool)$this->input->get('delete') === true) {
                return $this->del($id);
            }
            
            try
            {
                $this->load->library('form_validation');
            
                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }
                
                $this->category_model->edit($id, $this->input->post());
                $this->flash->success(Consts::SUCC_EDIT_CATEGORY);
                return redirect($this->referer->getSession());
            }
            catch (AppException $e)
            {
                $this->flash->error($e->getMessage());
            }
        }
        else {
            $category_data = $this->category_model->get($id);
            
            if (empty($category_data)) {
                show_error(Consts::ERR_NOT_FOUND);
            }
            $_POST = $category_data;
            
            // Lưu referer của page access đến form
            $this->referer->saveSession();
        }

        $view_data['title'] = 'Sửa danh mục';
        $view_data['select'] = [
            'inout_types' => $this->inout_type_model->get_select_tag_data(),
        ];
        $view_data['url'] = [
            'form'      => $this->base_url([__FUNCTION__, $id]),
            'del'       => $this->base_url(['del', $id]),
            'back'      => $this->referer->getSession(null, false),
        ];
        
        $this->template->write_view('MAIN', 'category/form', $view_data);
        $this->template->render();
    }
    
    public function del($id)
    {
        if (!is_numeric($id)) {
            show_error(Consts::ERR_BAD_REQUEST);
        }
        
        $this->category_model->del($id);
        $this->flash->success(Consts::SUCC_DEL_CATEGORY);
        return redirect($this->referer->getSession());
    }
}
