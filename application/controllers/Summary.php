<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Summary extends CI_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        if ($this->login_model->isLogin() === false){
            redirect(base_url().'login');
        }
    }
    
    public function index()
    {
        $this->template->write_view('MAIN', 'subNav/summary');
        $this->template->render();
    }
    
	public function recordSummary()
    {   
		$this->template->write_view('MAIN', 'record-summary');
        $this->template->render();
	}
    
    public function recordDetail()
    {
		$this->template->write_view('MAIN', 'record-detail');
        $this->template->render();
    }
    
}
