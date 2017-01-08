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
        $thisMonth = date('Y-m');
        return redirect($this->base_url(array('summary/list/', $thisMonth)));
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
    public function summary(string $view = null, string $date = null): void
    {   
        try 
        {
            $view = strtolower($view);
            $redirect = false;
            
            if (!in_array($view, array('list', 'chart'))) {
                $view = 'list';
                $redirect = true;
            }
            
            if ($redirect === true) {
                redirect($this->base_url(array(__FUNCTION__, $view, $date)));
            }
            
            $view_data = $this->_view_data_summary($view, $date);
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
     * Trang danh sách chi tiết thu chi theo ngày
     *
     * @param   string: thời gian muốn xem danh sách, có thể nhận format:
     *                      - yyyy-mm-dd
     *                      - yyyy-mm
     *                      - yyyy
     * @return  void
     *--------------------------------------------------------------------
     */
    public function inouts_of_day($view = null, $date = null)
    {
        try 
        {   
            $view = strtolower($view);
            
            if (!in_array($view, array('list', 'chart'))) {
                throw new Exception(Constants::ERR_BAD_REQUEST);
            }
            
            if (empty($date)) {
                throw new Exception(Constants::ERR_BAD_REQUEST);
            }
            
            $view_data = $this->_view_data_inouts_of_day($view, $date);
            
            $this->template->write_view('MAIN', 'viewlist/inouts_of_day_header', $view_data);
            $this->template->write_view('MAIN', 'viewlist/inouts_of_day_'.$view, $view_data);
            $this->template->render();
        }
        catch (Exception $e)
        {
            show_error($e->getMessage());
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
     * @return   array
     *--------------------------------------------------------------------
     */
    protected function _view_data_summary(string $view, ?string $date): array
    {   
        $extractedDate = extract_date_string($date);
        $mode = $this->_summary_inout_types_mode($extractedDate);
        
        $yearsInDB  = $this->viewlist_model->getYearsList();
        $monthsList = range(1, 12);
        
        $view_data['list'] = call_user_func_array(
            array($this->viewlist_model, 'summaryInoutTypesBy' . ucfirst($mode)), 
            $extractedDate
        );
        $view_data['page_scroll_target'] = $this->_page_scroll_target($mode);
        $view_data['year']  = $extractedDate['y'];
        $view_data['month'] = $extractedDate['m'];
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
                'list'  => $this->base_url(array($this->router->fetch_method(), 'list', $date)).query_string(),
                'chart' => $this->base_url(array($this->router->fetch_method(), 'chart', $date)).query_string(),
            ),
            'inouts_of_day' => $this->base_url(array('inouts_of_day', '%s', '%s')),
        );
        
        return $view_data;
	}

    /*
     *--------------------------------------------------------------------
     * Tạo view_data cho method "inouts_of_day"
     *
     * @param    string: hiển thị theo list hay chart
     * @param    string: đối tượng tổng kết: 
     *                       - ngày (yyyy-mm-dd)
     *                       - tháng (yyyy-mm)
     *                       - năm (yyyy)
     * @return   array
     *--------------------------------------------------------------------
     */
    protected function _view_data_inouts_of_day(string $view, string $date): array 
    {
        if (empty($range = boundary_date($date))){
            throw new Exception(Constants::ERR_BAD_REQUEST);
        }
        
        // Lấy thông tin từ request parameter
        $account_id     = $this->input->get('account')?? 0;
        $player_id      = $this->input->get('player')?? 0;
        $inout_type_id  = $this->input->get('inout_type')?? array_flip(Inout_model::$INOUT_TYPE)['Chi'];
        
        $dateChange = $this->viewlist_model->getPrevNextTime($date);
        
        $view_data = array();
        $view_data = array_merge($view_data, compact('account_id', 'player_id', 'inout_type_id'));
        $view_data['date'] = $date;
        $view_data['list'] = $view === 'list'
                             ? $this->viewlist_model->getInoutsOfDay($range[0], $range[1], $account_id, $player_id)
                             : $this->viewlist_model->summaryCategories($range[0], $range[1], $inout_type_id);
        $view_data['total_items'] = count($view_data['list']);
        $view_data['select'] = array(
            'accounts'    => $this->account_model->getSelectTagData(),
            'players'     => $this->user_model->getSelectTagData(),
            'inout_types' => $this->inout_type_model->getSelectTagData(), 
        );
        $view_data['url'] = array(
            'form'       => $this->base_url(array($this->router->fetch_method(), $view, $date)),
            'edit'       => base_url(array('inout', 'edit', '%s')),
            'dateChange' => array(
                'prev'   => $this->base_url(array($this->router->fetch_method(), $view, $dateChange[0])).query_string(),
                'next'   => $this->base_url(array($this->router->fetch_method(), $view, $dateChange[1])).query_string(),
            ),
            'navTabs'    => array(
                'list'   => $this->base_url(array($this->router->fetch_method(), 'list', $date)),
                'chart'  => $this->base_url(array($this->router->fetch_method(), 'chart', $date)),
            ),
        );
        
        return $view_data;
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy mode summary của xử lý summary theo inout type
     * 
     * @param   array: array chứa year, month, day đã tách ra từ date string.
     * @return  string: mode để tạo tên method đầy đủ.
     *--------------------------------------------------------------------
     */
    protected function _summary_inout_types_mode(array $extractedDate): string
    {
        $notNullCount = count(array_filter($extractedDate, function($item){
            return $item !== null;
        }));
        
        switch ($notNullCount) {
            case 0:
                return 'year';
            case 1:
                return 'monthInYear';
            case 2:
            case 3:
                return 'dayInMonth';
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
    protected function _page_scroll_target(string $mode): ?string
    {
        switch($mode){
            case 'day':
                return date('Y-m-d');
            case 'month':
                return date('Y-m');
            case 'year':
                return date('Y');
            default:
                return null;
        }
    }
}
