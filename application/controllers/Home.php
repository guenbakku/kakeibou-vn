<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    
	public function index()
    {   
        $view_data['month_sum'] = current($this->viewlist_model->getSumListFromDB('%Y-%m', date('Y-m'), date('Y-m')));
        $view_data['liquidOutgoStatus'] = $this->viewlist_model->getLiquidOutgoStatus();
        $view_data['remaining'] = $this->viewlist_model->getRemaining();
		$this->template->write_view('MAIN', 'home', $view_data);
        $this->template->render();
	}
    
}
