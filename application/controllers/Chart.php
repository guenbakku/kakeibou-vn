<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chart extends MY_Controller {
    
    protected $ctrl_base_url = 'timeline';
    
    public function __construct()
    {   
        parent::__construct();
        $this->load->model('timeline_model');
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
    public function line(?string $date = null): void
    {   
        $extractedDate = extract_date_string($date);
        $dateFormatType = date_format_type_of_string($date);
        $dateChange = prev_next_time($date);
        $yearsList  = $this->timeline_model->get_years_list();
        $monthsList = months_list();
        
        $view_data['title'] = 'Biểu đồ đường';
        $view_data['list'] = $this->timeline_model->summary_inout_types_auto($extractedDate['y'], $extractedDate['m']);
        $view_data['list'] = $this->timeline_model->calcCumulative($view_data['list']);
        $view_data['year']  = $extractedDate['y']?? '';
        $view_data['month'] = $extractedDate['m']?? '';
        $view_data['select'] = [
            'year' => array_combine($yearsList, $yearsList),
            'month' => array_combine($monthsList, $monthsList),
        ];
        $view_data['url'] = [
            'dateSelectionForm' => $this->base_url([$this->router->fetch_method()]),
            'back'              => base_url(),
            'dateChange'        => [
                'prev'          => $this->base_url([$this->router->fetch_method(), $dateChange[0]]).query_string(),
                'next'          => $this->base_url([$this->router->fetch_method(), $dateChange[1]]).query_string(),
            ],
            'timeline'          => base_url(['timeline', $this->router->fetch_method(), $date]),
        ];
        $view_data = array_merge($view_data, compact('date', 'dateFormatType'));
        
        $this->template->write_view('MAIN', 'chart/line', $view_data);
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
    public function pie(?string $date = null)
    {
        if (empty($date)) {
            show_error(Consts::ERR_BAD_REQUEST);
        }
        
        if (empty($range = boundary_date($date))) {
            show_error(Consts::ERR_BAD_REQUEST);
        }
        
        // Lấy thông tin từ request parameter
        $account_id     = $this->input->get('account')?? 0;
        $player_id      = $this->input->get('player')?? 0;
        $inout_type_id  = $this->input->get('inout_type')?? array_flip(Inout_model::$INOUT_TYPE)['Chi'];
        
        $extractedDate = extract_date_string($date);
        $dateFormatType = date_format_type_of_string($date);
        $dateChange = prev_next_time($date);
        $yearsList  = $this->timeline_model->get_years_list();
        $monthsList = months_list();
        $daysList   = days_list();
        
        $view_data['title'] = 'Biểu đồ quạt';
        $view_data['list'] = $this->timeline_model->summary_categories($range[0], $range[1], $inout_type_id);
        $view_data['year']  = $extractedDate['y']?? '';
        $view_data['month'] = $extractedDate['m']?? '';
        $view_data['day']   = $extractedDate['d']?? '';
        $view_data['total_items'] = count($view_data['list']);
        $view_data['select'] = array(
            'accounts'    => $this->account_model->get_select_tag_data(),
            'players'     => $this->user_model->get_select_tag_data(),
            'inout_types' => $this->inout_type_model->get_select_tag_data(),
            'year'        => array_combine($yearsList, $yearsList),
            'month'       => array_combine($monthsList, $monthsList),
            'day'         => array_combine($daysList, $daysList),
        );
        $view_data['url'] = array(
            'dateSelectionForm' => $this->base_url([$this->router->fetch_method()]),
            'subForm'           => $this->base_url([$this->router->fetch_method(), $date]),
            'back'              => base_url(),
            'dateChange'        => [
                'prev'          => $this->base_url([$this->router->fetch_method(), $dateChange[0]]).query_string(),
                'next'          => $this->base_url([$this->router->fetch_method(), $dateChange[1]]).query_string(),
            ],
            'timeline'          => base_url(['timeline', $this->router->fetch_method(), $date]),
        );
        $view_data = array_merge($view_data, compact('date', 'dateFormatType', 'account_id', 'player_id', 'inout_type_id'));
        
        $this->template->write_view('MAIN', 'chart/pie', $view_data);
        $this->template->render();
    }
}
