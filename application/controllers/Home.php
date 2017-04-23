<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Controller {
    
	public function index()
    {   
        $view_data['month_sum'] = current($this->timeline_model->summary_inout_types(date('Y-m-01'), date('Y-m-t'), '%Y-%m'));
        $view_data['liquidOutgoStatus'] = $this->timeline_model->getLiquidOutgoStatus();
        $view_data['remaining'] = $this->timeline_model->getRemaining();
        $view_data['url'] = [
            'detailToday' => base_url(['timeline', 'detail', date('Y-m-d')]),
            'summaryThisMonth' => base_url(['timeline', 'summary', date('Y-m')]),
        ];
		$this->template->write_view('MAIN', 'home/home', $view_data);
        $this->template->render();
	}
}
