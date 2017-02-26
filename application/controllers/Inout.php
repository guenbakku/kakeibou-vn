<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inout extends MY_Controller {
        
    public function index()
    {
        $this->template->write_view('MAIN', 'inout/menu');
        $this->template->render();
    }
    
	public function add($type=null)
    {   
        if (!$cashFlowName = $this->inout_model->getCashFlowName($type)) {
            show_error(Consts::ERR_BAD_REQUEST);
        }

        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            try {
                $this->load->library('form_validation');
                
                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }
                
                $this->inout_model->add($type, $this->input->post());
                
                $this->flash->success(sprintf(Consts::SUCC_ADD_INOUT_RECORD, $this->inout_model->getCashFlowName($type)));
                
                // Xét xem có nhập tiếp hay không
                if ((bool)$this->input->get('continue') === false) {
                    return redirect(base_url());
                } else {
                    $this->form_validation->reset_field_data(['amount', 'memo']);
                }
            }
            catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
            
        }
        
        $view_data['type']            = $type;
        $view_data['title']           = $cashFlowName;
        $view_data['inout_type_sign'] = $this->inout_model->getInoutTypeSign($type);
        $view_data['select']   = array(
            'accounts'   => $this->account_model->getSelectTagData(),
            'players'    => $this->user_model->getSelectTagData(),
            'categories' => $this->category_model->getSelectTagData($this->inout_model->getInoutTypeCode($type)),
        );
        $view_data['url']   = array(
            'form'      => $this->base_url(array(__FUNCTION__, $type)),
            'back'      => base_url(),
        );

		$this->template->write_view('MAIN', 'inout/form', $view_data);
        $this->template->render();
	}
    
    public function edit($id=null)
    {
        if (!is_numeric($id)) {
            show_error(Consts::ERR_BAD_REQUEST);
        }
        
        $ioRecord = $this->inout_model->get($id);
        
        if (empty($ioRecord)) {
            show_error(Consts::ERR_NOT_FOUND);
        }
        
        if ($this->input->server('REQUEST_METHOD') == 'POST') {
            // Chuyển sang xử lý xóa record nếu lựa chọn xóa
            if ((bool)$this->input->get('delete') === true) {
                return $this->del($id);
            }
            
            try {
                $this->load->library('form_validation');
                
                if ($this->form_validation->run() === false) {
                    throw new AppException(validation_errors());
                }
                
                $this->inout_model->edit($id, $this->input->post());
                $this->flash->success(Consts::SUCC_EDIT_INOUT_RECORD);
                return redirect($this->referer->getSession());
            }
            catch (AppException $e) {
                $this->flash->error($e->getMessage());
            }
        }
        else {
            // Lưu referer của page access đến form 
            $this->referer->saveSession();
        }
        
        if ($ioRecord['cash_flow'] == 'handover') {
            $ioRecord['player'] = $this->inout_model->setPlayersForHandoverEdit($ioRecord);
        }
        
        $ioRecord['amount'] = abs($ioRecord['amount']);
        $_POST = $ioRecord;
        $type = $ioRecord['cash_flow'];
        $view_data                    = $ioRecord;
        $view_data['type']            = $type;
        $view_data['title']           = 'Chỉnh sửa';
        $view_data['inout_type_sign'] = $this->inout_model->getInoutTypeSign($ioRecord['inout_type_id']);
        $view_data['select']   = array(
            'accounts'   => $this->account_model->getSelectTagData(),
            'players'    => $this->user_model->getSelectTagData(),
            'categories' => $this->category_model->getSelectTagData($this->inout_model->getInoutTypeCode($type)),
        );
        $view_data['url']   = array(
            'form'      => $this->base_url(array(__FUNCTION__, $id)),
            'back'      => base_url(),
        );
        
		$this->template->write_view('MAIN', 'inout/form', $view_data);
        $this->template->render();
    }
    
    public function del($id=null)
    {
        if (!is_numeric($id)) {
            show_error(Consts::ERR_BAD_REQUEST);
        }
        
        $this->inout_model->del($id);
        $this->flash->success(Consts::SUCC_DELETE_INOUT_RECORD);
        redirect($this->referer->getSession());
    }
    
    public function searchMemo($q)
    {
        echo json_encode($this->inout_model->searchMemo(urldecode($q)));
    }
}
