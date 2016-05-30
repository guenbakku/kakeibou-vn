<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Record extends CI_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        if ($this->login_model->isLogin() === false){
            redirect(base_url().'login');
        }
    }    
    
    public function index()
    {
        $this->template->write_view('MAIN', 'subNav/record');
        $this->template->render();
    }
    
	public function add($type)
    {   
        
        if (!$this->getCashFlowName($type)){
            show_404();
        }
        
        $data['type']     = $type;
        $data['title']    = $this->getCashFlowName($type);
        $data['form_url'] = base_url()."record/";
        
		$this->template->write_view('MAIN', 'record-form', $data);
        $this->template->render();
	}
    
    public function edit($num)
    {
        $data['title']    = 'Chỉnh sửa';
        $data['form_url'] = base_url()."record/";
        
		$this->template->write_view('MAIN', 'record-form', $data);
        $this->template->render();
    }
    
    /*
     *--------------------------------------------------------------------
     *
     * @return  string  : tên phân loại giao dịch
     *--------------------------------------------------------------------
     */
    private function getCashFlowName($type)
    {   
        $titleArr = array(
            'outgo'     => 'Thêm mới khoản chi',
            'income'    => 'Thêm mới khoản thu',
            'drawer'    => 'Rút tiền từ tài khoản',
            'deposit'   => 'Nạp tiền vô tài khoản',
            'handover'  => 'Chuyển tiền qua tay',
            'transfer'  => 'Chuyển khoản',
        );
        
        $type = strtolower($type);
        if (isset($titleArr[$type])){
            return $titleArr[$type];
        }
        else {
            return false;
        }
    }
}
