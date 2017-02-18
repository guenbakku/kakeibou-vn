<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    
	public function index()
    {   
        $view_data['month_sum'] = current($this->viewlist_model->summaryInoutTypes(date('Y-m-01'), date('Y-m-t'), '%Y-%m'));
        $view_data['liquidOutgoStatus'] = $this->viewlist_model->getLiquidOutgoStatus();
        $view_data['remaining'] = $this->viewlist_model->getRemaining();
        $view_data['url'] = [
            'detailToday' => base_url(['viewlist', 'detail', date('Y-m-d')]),
            'summaryThisMonth' => base_url(['viewlist', 'summary', date('Y-m')]),
        ];
		$this->template->write_view('MAIN', 'home/home', $view_data);
        $this->template->render();
	}
    
    public function test()
    {   
        $this->load->library('auth');
        $this->auth->set_verify_info(['username'=>'test', 'password'=>'test']);
        d($this->auth->authenticate());
        d($this->auth->add_token_to_db(1));
        // d($this->auth->is_authenticated());
    }
    
    public function test2()
    {
        d(random_string('sha1'));
        d($this->session->userdata());
    }
}
