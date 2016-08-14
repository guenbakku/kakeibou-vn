<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Viewlist extends MY_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        $this->load->model('viewlist_model');
    }
    
    public function index()
    {
        // $this->template->write_view('MAIN', 'viewlist/menu');
        // $this->template->render();
        redirect(base_url().'viewlist/summary/day');
    }
    
    public function summary($mode=null)
    {   
        try 
        {
            if ($mode==null){
                redirect(my_site_url(__CLASS__, __FUNCTION__, 'day'));
            }
            
            if (!in_array($mode, array('day', 'month', 'year'))){
                throw new Exception(Constants::ERR_BAD_REQUEST);
            }
            
            // Lấy biến từ $_GET;
            $year = $this->input->get('year');
            $year = $year === null? date('Y') : (int)$year;
            $month = $this->input->get('month');
            $month = $month === null? date('m') : (int)$month;
            
            $yearsInDB = $this->viewlist_model->getYearsListInDB();
            
            $view_data['list'] = call_user_func_array(
                array($this->viewlist_model, __FUNCTION__ . '_by_' . $mode), 
                array($year, $month)
            );
            $view_data['year'] = $year;
            $view_data['month'] = $month;
            $view_data['mode'] = $mode;
            $view_data['select'] = array(
                'year' => array_combine($yearsInDB, $yearsInDB),
                'month' => array_combine(range(1,12), range(1,12)),
            );
            $view_data['url'] = array(
                'form'          => $this->base_url(array(__FUNCTION__, $mode)),
                'subNav'        => array(
                    $this->base_url(array(__FUNCTION__, 'day')),
                    $this->base_url(array(__FUNCTION__, 'month')),
                    $this->base_url(array(__FUNCTION__, 'year')),
                ),
                'inouts_of_day' => $this->base_url(array('inouts_of_day', '%s')),
            );
            
            $this->template->write_view('MAIN', 'viewlist/summary', $view_data);
            $this->template->render();
        }
        catch (Exception $e)
        {
            show_error($e->getMessage());
        }
	}
    
    public function inouts_of_day($date)
    {
        try 
        {
            if (!preg_match('/^\d{4}(\-\d{2})?(\-\d{2})?$/', $date)
                || !strtotime($date) )
            {
                throw new Exception(Constants::ERR_BAD_REQUEST);
            }

            // Tính giới hạn thời gian
            $mdate = str_replace('-', '', $date);
            if (preg_match('/^\d{8}$/', $mdate)){
                $range = array(
                    date('Y-m-d', strtotime($mdate)), 
                    date('Y-m-d', strtotime($mdate)), 
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
                    date('Y-m-d', strtotime($mdate.'0101')),
                    date('Y-m-d', strtotime($mdate.'1231')),
                );
            }
            
            // Lấy thông tin về tài khoản và loại thu chi
            $account = $this->input->get('account');
            $player  = $this->input->get('player');
            if ($account === null) $account = 0;
            if ($player === null) $player = 0;
            
            $view_data['date'] = $date;
            $view_data['list'] = $this->viewlist_model->getInoutsOfDay($range[0], $range[1], $account, $player);
            $view_data['account'] = $account;
            $view_data['player']  = $player;
            $view_data['total_items'] = count($view_data['list']);
            $view_data['select']   = array(
                'accounts' => $this->account_model->getSelectTagData(),
                'players'  => $this->user_model->getSelectTagData(),
            );
            $view_data['url'] = array(
                'form'  => $this->base_url(array(__FUNCTION__, $date)),
                'edit'  => base_url(array('inout', 'edit', '%s')),
            );
            
            $this->template->write_view('MAIN', 'viewlist/inouts_of_day', $view_data);
            $this->template->render();
        }
        catch (Exception $e)
        {
            show_error($e->getMessage());
        }
    }
}
