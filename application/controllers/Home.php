<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    
	public function index()
    {           
        $view_data['month_sum'] = current($this->viewlist_model->summary_inout_types('%Y-%m', date('Y-m'), date('Y-m')));
        $view_data['liquidOutgoStatus'] = $this->viewlist_model->getLiquidOutgoStatus();
        $view_data['remaining'] = $this->viewlist_model->getRemaining();
        $view_data['url'] = array(
            'summary_this_month' => base_url(array('viewlist', 'summary', 'list', 'day')),
            'inouts_of_today' => base_url(array('viewlist', 'inouts_of_day', date('Y-m-d'))),
        );
		$this->template->write_view('MAIN', 'home/home', $view_data);
        $this->template->render();
	}
}
