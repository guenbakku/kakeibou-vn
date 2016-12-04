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
        return redirect($this->base_url('summary/list/day'));
    }
    
    /**
     *--------------------------------------------------------------------
     * Trang tổng kết số tiền thu chi trong theo ngày trong tháng, 
     * tháng trong năm và năm
     *
     * @param    string: hiển thị theo list hay chart
     * @param    string: đối tượng tổng kết: 
     *                       - ngày trong tháng, 
     *                       - tháng trong năm, 
     *                       - năm
     * @return   void
     *--------------------------------------------------------------------
     */
    public function summary($view=null, $mode=null) 
    {   
        try 
        {
            $view = strtolower($view);
            $mode = strtolower($mode);
            $redirect = false;
            if (!in_array($view, array('list', 'chart'))) {
                $view = 'list';
                $redirect = true;
            }
            
            if (!in_array($mode, array('day', 'month', 'year'))){
                $mode = 'day';
                $redirect = true;
            }
            
            if ($redirect === true) {
                redirect($this->base_url(array(__FUNCTION__, $view, $mode)));
            }
            
            $view_data = $this->_summary_view_data($view, $mode);
            $this->template->write_view('MAIN', 'viewlist/summary_header', $view_data);
            $this->template->write_view('MAIN', 'viewlist/summary_'.$view, $view_data);
            $this->template->render();
        } 
        catch (Exception $ex) {
            show_error($ex->getMessage());
        }
        
    }
    
    /**
     *--------------------------------------------------------------------
     * Tạo view_data cho method "summary"
     *
     * @param    string: hiển thị theo list hay chart
     * @param    string: đối tượng tổng kết: 
     *                       - ngày trong tháng, 
     *                       - tháng trong năm, 
     *                       - năm
     * @return   void
     *--------------------------------------------------------------------
     */
    protected function _summary_view_data($view, $mode)
    {   
        // Lấy biến từ $_GET;
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        if ($year === null) $year = date('Y');
        if ($month === null) $month = date('m');
        
        $yearsInDB  = $this->viewlist_model->getYearsListInDB();
        $monthsList = range(1, 12);
        
        $view_data['list'] = call_user_func_array(
            array($this->viewlist_model, 'summary_by_' . $mode), 
            array($year, $month)
        );
        $view_data['page_scroll_target'] = $this->_page_scroll_target($mode);
        $view_data['year'] = $year;
        $view_data['month'] = $month;
        $view_data['mode'] = $mode;
        $view_data['select'] = array(
            'year' => array_combine($yearsInDB, $yearsInDB),
            'month' => array_combine($monthsList, $monthsList),
        );
        $view_data['url'] = array(
            'form'     => $this->base_url(array($this->router->fetch_method(), $view, $mode)),
            'btnGroup' => array(
                'day'   => $this->base_url(array($this->router->fetch_method(), $view, 'day')),
                'month' => $this->base_url(array($this->router->fetch_method(), $view, 'month')),
                'year'  => $this->base_url(array($this->router->fetch_method(), $view, 'year')),
            ),
            'navTabs'  => array(
                'list'  => $this->base_url(array($this->router->fetch_method(), 'list', $mode)),
                'chart' => $this->base_url(array($this->router->fetch_method(), 'chart', $mode)),
            ),
            'inouts_of_day' => $this->base_url(array('inouts_of_day', '%s')),
        );
        
        return $view_data;
	}
    
    /**
     *--------------------------------------------------------------------
     * Trang danh sách chi tiết thu chi theo ngày
     *
     * @param   string: thời gian muốn xem danh sách, có thể nhận format:
     *                      - yyyy-mm-dd
     *                      - yyyy-mm
     *                      - yyyy
     * @return  void
     *--------------------------------------------------------------------
     */
    public function inouts_of_day($date)
    {
        try 
        {
            if (false === $range = $this->viewlist_model->getBoundaryDate($date)){
                throw new Exception(Constants::ERR_BAD_REQUEST);
            }
            
            // Lấy thông tin về tài khoản và loại thu chi
            $account = $this->input->get('account');
            $player  = $this->input->get('player');
            if ($account === null) $account = 0;
            if ($player === null) $player = 0;
            
            $prevNext = $this->viewlist_model->getPrevNextTime($date);

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
                'form'  => $this->base_url(array($this->router->fetch_method(), $date)),
                'edit'  => base_url(array('inout', 'edit', '%s')),
                'prev'  => $this->base_url(array($this->router->fetch_method(), $prevNext[0])).query_string(),
                'next'  => $this->base_url(array($this->router->fetch_method(), $prevNext[1])).query_string(),
            );
            
            $this->template->write_view('MAIN', 'viewlist/inouts_of_day', $view_data);
            $this->template->render();
        }
        catch (Exception $e)
        {
            show_error($e->getMessage());
        }
    }

    /*
     *--------------------------------------------------------------------
     * Tạo page-scroll target đến ngày/tháng/năm hiện tại tùy vào kiểu danh sách
     *
     * @param   string  : kiểu danh sách
     * @param   string  : thời điểm hiện tại để scroll đến
     *--------------------------------------------------------------------
     */
    private function _page_scroll_target($mode)
    {
        switch($mode){
            case 'day':
                return date('Y-m-d');
            case 'month':
                return date('Y-m');
            case 'year':
                return date('Y');
            default:
                return false;
        }
    }
}
