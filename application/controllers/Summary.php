<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Summary extends CI_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        if ($this->login_model->isLogin() === false){
            redirect(base_url().'login');
        }
        
        $this->load->model('summary_model');
    }
    
    public function index()
    {
        $this->template->write_view('MAIN', 'summary/menu');
        $this->template->render();
    }
    
    public function viewList($mode=null)
    {   
        if ($mode==null){
            redirect(my_site_url(__CLASS__, __FUNCTION__, 'day'));
        }
        
        if (!in_array($mode, array('day', 'month', 'weak', 'year'))){
            show_error(Constants::ERR_BAD_REQUEST);
        }
        
        $view_data = call_user_func(array($this, __FUNCTION__ . '_' . $mode));
        $view_data['form_url'] = my_site_url(__CLASS__, __FUNCTION__, $mode);
        
        $this->template->write_view('MAIN', 'summary/viewlist', $view_data);
        $this->template->render();
	}
    
    public function detail()
    {
		$this->template->write_view('MAIN', 'summary/detail');
        $this->template->render();
    }
    
    /*
     *====================================================================
     * Private Method
     *====================================================================
     */
    private function viewList_day()
    {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        
        if ($year==null || $month === null){
            $year = date('Y');
            $month = date('m');
        }
        
        if (!is_numeric($year) || !is_numeric($month)
            || $year < 0
            || $month < 1 || $month > 12){
            show_error(Constants::ERR_BAD_REQUEST);
        }
        
        $view_data['list'] = $this->summary_model->getListByDay($year, $month);
        $view_data['year'] = $year;
        $view_data['month'] = $month;
        $view_data['mode'] = 'day';
        $view_data['select'] = array(
            'year' => $this->app_model->getSelectTagData('yearsInDB'),
            'month' => array_combine(range(1,12), range(1,12)),
        );
        
        return $view_data;
    }
    
    private function viewList_month()
    {
        $year = $this->input->get('year');
        
        if ($year==null){
            $year = date('Y');
        }
        
        if (!is_numeric($year)){
            show_error(Constants::ERR_BAD_REQUEST);
        }
        
        $view_data['list'] = $this->summary_model->getListByMonth($year);
        $view_data['year'] = $year;
        $view_data['mode'] = 'month';
        $view_data['select'] = array(
            'year' => $this->app_model->getSelectTagData('yearsInDB'),
        );
        
        return $view_data;
    }
    
    private function viewList_year()
    {
        $view_data['list'] = $this->summary_model->getListByYear();
        $view_data['mode'] = 'year';
        
        return $view_data;
    }
}
