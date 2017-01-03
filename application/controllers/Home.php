<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    
	public function index()
    {   
        $view_data['month_sum'] = current($this->viewlist_model->summaryInoutTypes(date('Y-m-01'), date('Y-m-t'), '%Y-%m'));
        $view_data['liquidOutgoStatus'] = $this->viewlist_model->getLiquidOutgoStatus();
        $view_data['remaining'] = $this->viewlist_model->getRemaining();
        $view_data['url'] = array(
            'inouts_of_today' => base_url(array('viewlist', 'inouts_of_day', 'list', date('Y-m-d'))),
            'summary_this_month' => base_url(array('viewlist', 'summary', 'list', 'day')),
        );
		$this->template->write_view('MAIN', 'home/home', $view_data);
        $this->template->render();
	}
}
