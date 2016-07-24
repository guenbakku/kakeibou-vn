<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        if ($this->login_model->isLogin() === false){
            redirect(base_url().Login_model::LOGIN_URL);
        }
    }
    
	public function index()
    {   
        $view_data['month_sum'] = current($this->viewlist_model->getSumListFromDB('%Y-%m', date('Y-m'), date('Y-m')));
        $view_data['liquidOutgoStatus'] = $this->viewlist_model->getLiquidOutgoStatus();
        $view_data['remaining'] = $this->viewlist_model->getRemaining();
		$this->template->write_view('MAIN', 'home', $view_data);
        $this->template->render();
	}
    
}
