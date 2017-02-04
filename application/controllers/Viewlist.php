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
        // $this->template->write_view('MAIN', 'viewlist/menu', $view_data);
        // $this->template->render();
        return redirect($this->base_url(['summary', date('Y-m')]));
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
    public function summary(?string $date = null): void
    {        
        $extractedDate = extract_date_string($date);
        $dateFormatType = date_format_type_of_string($date);
        $dateChange = prev_next_time($date);
        $yearsList  = $this->viewlist_model->getYearsList();
        $monthsList = months_list();
        
        $view_data['list'] = $this->viewlist_model->summaryInoutTypesAutoDetect($extractedDate['y'], $extractedDate['m']);
        $view_data['year'] = $extractedDate['y']?? '';
        $view_data['month'] = $extractedDate['m']?? '';
        $view_data['select'] = [
            'year'  => array_combine($yearsList, $yearsList),
            'month' => array_combine($monthsList, $monthsList),
        ];
        $view_data['url'] = [
            'dateSelectionForm' => $this->base_url($this->router->fetch_method()),
            'back'              => base_url(),
            'dateChange'        => [
                'prev'          => $this->base_url([$this->router->fetch_method(), $dateChange[0]]).query_string(),
                'next'          => $this->base_url([$this->router->fetch_method(), $dateChange[1]]).query_string(),
            ],
            'viewchart'         => base_url(['chart', $this->router->fetch_method(), $date]),
            'detailTemplate'   => $this->base_url(['detail', '%s']),
        ];
        $view_data['pageScrollTarget'] = $this->_page_scroll_target($date);
        $view_data = array_merge($view_data, compact('date', 'dateFormatType'));
        
        $this->template->write_view('MAIN', 'viewlist/summary', $view_data);
        $this->template->render();
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
    public function detail(?string $date = null): void
    {
        if (empty($date)) {
            show_error(Constants::ERR_BAD_REQUEST);
        }
        if (empty($range = boundary_date($date))){
            show_error(Constants::ERR_BAD_REQUEST);
        }
        
        // Lấy thông tin từ request parameter
        $account_id     = $this->input->get('account')?? 0;
        $player_id      = $this->input->get('player')?? 0;
        $inout_type_id  = $this->input->get('inout_type')?? array_flip(Inout_model::$INOUT_TYPE)['Chi'];
        
        $extractedDate = extract_date_string($date);
        $dateFormatType = date_format_type_of_string($date);
        $dateChange = prev_next_time($date);
        $yearsList  = $this->viewlist_model->getYearsList();
        $monthsList = months_list();
        $daysList   = days_list();
        
        $view_data['list']  = $this->viewlist_model->getInoutsOfDay($range[0], $range[1], $account_id, $player_id);
        $view_data['year']  = $extractedDate['y']?? '';
        $view_data['month'] = $extractedDate['m']?? '';
        $view_data['day']   = $extractedDate['d']?? '';
        $view_data['total_items'] = count($view_data['list']);
        $view_data['select'] = [
            'accounts'    => $this->account_model->getSelectTagData(),
            'players'     => $this->user_model->getSelectTagData(),
            'inout_types' => $this->inout_type_model->getSelectTagData(),
            'year'        => array_combine($yearsList, $yearsList),
            'month'       => array_combine($monthsList, $monthsList),
            'day'         => array_combine($daysList, $daysList),
        ];
        $view_data['url'] = [
            'dateSelectionForm' => $this->base_url([$this->router->fetch_method()]),
            'subForm'           => $this->base_url([$this->router->fetch_method(), $date]),
            'back'              => base_url(),
            'editTemplate'      => base_url(['inout', 'edit', '%s']),
            'dateChange'        => [
                'prev'          => $this->base_url([$this->router->fetch_method(), $dateChange[0]]).query_string(),
                'next'          => $this->base_url([$this->router->fetch_method(), $dateChange[1]]).query_string(),
            ],
            'viewchart'         => base_url(['chart', $this->router->fetch_method(), $date]),
        ];
        $view_data = array_merge($view_data, compact('date', 'dateFormatType', 'account_id', 'player_id', 'inout_type_id'));
        
        
        $this->template->write_view('MAIN', 'viewlist/detail', $view_data);
        $this->template->render();
    }
    
    /*
     *--------------------------------------------------------------------
     * Tạo page-scroll target đến ngày/tháng/năm hiện tại tùy vào kiểu danh sách
     *
     * @param   string  : kiểu danh sách
     * @param   string  : thời điểm hiện tại để scroll đến
     *--------------------------------------------------------------------
     */
    protected function _page_scroll_target(?string $date): ?string
    {
        $format_type = date_format_type_of_string($date);
        
        switch ($format_type) {
            case null:
                return date('Y');
            case 'y':
                return date('Y-m');
            case 'ym':
            case 'ymd':
                return date('Y-m-d');
        }
    }
}
