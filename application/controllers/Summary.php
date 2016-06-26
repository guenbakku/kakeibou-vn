<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Summary extends CI_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        if ($this->login_model->isLogin() === false){
            redirect($this->login_model->getLoginUrl());
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
    
    public function detail($date)
    {
        // Tính giới hạn thời gian
        $mdate = str_replace('-', '', $date);
        if (preg_match('/^\d{8}$/', $mdate)){
            $range = array(
                $mdate, 
                $mdate,
            );
        }
        elseif (preg_match('/^\d{6}$/', $mdate)){
            $range = array(
                date('Y-m-d', strtotime("{$mdate}01")),
                date('Y-m-t', strtotime("{$mdate}01")),
            );
        }
        elseif (preg_match('/^\d{4}$/', $mdate)){
            $range = array(
                $mdate.'0101',
                $mdate.'1231',
            );
        }
        else {
            show_error(Constants::ERR_BAD_REQUEST);
        }
        
        // Lấy thông tin về tài khoản và loại thu chi
        $account = $this->input->get('account');
        $player  = $this->input->get('player');
        if ($account === null) $account = 1;
        if ($player === null) $player = $this->login_model->getInfo('uid');
        
        $view_data['date'] = $date;
        $view_data['list'] = $this->summary_model->getDailyList($range[0], $range[1], $account, $player);
        $view_data['account'] = $account;
        $view_data['player']  = $player;
        $view_data['total_items'] = count($view_data['list']);
        $view_data['form_url'] = my_site_url(__CLASS__, __FUNCTION__, $date);
        $view_data['select']   = array(
            'accounts' => $this->app_model->getSelectTagData('account_id'),
            'players'  => $this->app_model->getSelectTagData('user_id'),
        );
        
		$this->template->write_view('MAIN', 'summary/detail', $view_data);
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
