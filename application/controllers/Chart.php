<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chart extends MY_Controller {
    
    public function __construct()
    {   
        parent::__construct();
        $this->load->model('viewlist_model');
    }
    
    public function index(string $date_selection_form = 'summary')
    {           
        $date_selection_form = strtolower($date_selection_form);
        if (!in_array($date_selection_form, ['summary', 'detail'])){
            show_error(Constants::ERR_BAD_REQUEST);
        }
        
        $yearsList  = $this->viewlist_model->getYearsList();
        $monthsList = months_list();
        $daysList   = days_list();
        
        $view_data['select'] = [
            'year'  => array_combine($yearsList, $yearsList),
            'month' => array_combine($monthsList, $monthsList),
            'day'   => array_combine($daysList, $daysList),
        ];
        $view_data['url'] = [
            'dateSelectionForm' => $this->base_url($date_selection_form),
            'back'              => base_url(),
            'navTabs'           => [
                'summary' => $this->base_url([$this->router->fetch_method(), 'summary']),
                'detail' => $this->base_url([$this->router->fetch_method(), 'detail']),
            ]
        ];
        $view_data = array_merge($view_data, compact('date_selection_form'));
        
        $this->template->write_view('MAIN', 'chart/menu', $view_data);
        $this->template->render();
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
        $mode = $this->_summary_inout_types_mode($extractedDate);
        
        $dateChange = prev_next_time($date);
        $yearsList  = $this->viewlist_model->getYearsList();
        $monthsList = months_list();
        
        $view_data['list'] = call_user_func_array(
            [$this->viewlist_model, 'summaryInoutTypesBy' . ucfirst($mode)], 
            $extractedDate
        );
        $view_data['year']  = $extractedDate['y']?? '';
        $view_data['month'] = $extractedDate['m']?? '';
        $view_data['select'] = [
            'year' => array_combine($yearsList, $yearsList),
            'month' => array_combine($monthsList, $monthsList),
        ];
        $view_data['url'] = [
            'dateSelectionForm' => $this->base_url([$this->router->fetch_method()]),
            'back'              => $this->base_url(['index', 'summary']),
            'dateChange'        => [
                'prev'          => $this->base_url([$this->router->fetch_method(), $dateChange[0]]).query_string(),
                'next'          => $this->base_url([$this->router->fetch_method(), $dateChange[1]]).query_string(),
            ],
            'viewlist'          => base_url(['viewlist', $this->router->fetch_method(), $date]),
        ];
        $view_data = array_merge($view_data, compact('mode', 'date'));
        
        $this->template->write_view('MAIN', 'chart/summary', $view_data);
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
    public function detail(?string $date = null)
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
        $dateChange    = prev_next_time($date);
        $yearsList     = $this->viewlist_model->getYearsList();
        $monthsList    = months_list();
        $daysList      = days_list();
        
        $view_data['list'] = $this->viewlist_model->summaryCategories($range[0], $range[1], $inout_type_id);
        $view_data['year']  = $extractedDate['y']?? '';
        $view_data['month'] = $extractedDate['m']?? '';
        $view_data['day']   = $extractedDate['d']?? '';
        $view_data['total_items'] = count($view_data['list']);
        $view_data['select'] = array(
            'accounts'    => $this->account_model->getSelectTagData(),
            'players'     => $this->user_model->getSelectTagData(),
            'inout_types' => $this->inout_type_model->getSelectTagData(),
            'year'        => array_combine($yearsList, $yearsList),
            'month'       => array_combine($monthsList, $monthsList),
            'day'         => array_combine($daysList, $daysList),
        );
        $view_data['url'] = array(
            'dateSelectionForm' => $this->base_url([$this->router->fetch_method()]),
            'subForm'           => $this->base_url([$this->router->fetch_method(), $date]),
            'back'              => $this->base_url(['index', 'detail']),
            'dateChange'        => [
                'prev'          => $this->base_url([$this->router->fetch_method(), $dateChange[0]]).query_string(),
                'next'          => $this->base_url([$this->router->fetch_method(), $dateChange[1]]).query_string(),
            ],
            'viewlist'          => base_url(['viewlist', $this->router->fetch_method(), $date]),
        );
        $view_data = array_merge($view_data, compact('date', 'account_id', 'player_id', 'inout_type_id'));
        
        $this->template->write_view('MAIN', 'chart/detail', $view_data);
        $this->template->render();
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy mode summary của xử lý summary theo inout type
     * 
     * @param   array: array chứa year, month, day đã tách ra từ date string.
     * @return  string: mode để tạo tên method đầy đủ.
     *--------------------------------------------------------------------
     */
    protected function _summary_inout_types_mode(array $extractedDate): ?string
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
            default:
                return null;
        }
    }
}
