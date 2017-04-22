<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    
	public function index()
    {   
        $view_data['month_sum'] = current($this->viewlist_model->summary_inout_types(date('Y-m-01'), date('Y-m-t'), '%Y-%m'));
        $view_data['liquidOutgoStatus'] = $this->viewlist_model->getLiquidOutgoStatus();
        $view_data['remaining'] = $this->viewlist_model->getRemaining();
        $view_data['url'] = [
            'detailToday' => base_url(['viewlist', 'detail', date('Y-m-d')]),
            'summaryThisMonth' => base_url(['viewlist', 'summary', date('Y-m')]),
        ];
		$this->template->write_view('MAIN', 'home/home', $view_data);
        $this->template->render();
	}
}
