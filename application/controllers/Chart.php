<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Chart extends MY_Controller
{
    protected $ctrl_base_url = 'timeline';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('timeline_model');
    }

    /**
     * Trang tổng kết số tiền thu chi theo
     *  - ngày trong tháng
     *  - tháng trong năm
     *  - năm.
     *
     * @param string $date thời gian muốn xem danh sách, có thể nhận format:
     *                     - yyyy-mm
     *                     - yyyy
     *                     - null
     */
    public function line(string $date = '')
    {
        $extractedDate = extract_date_string($date);
        $dateFormatType = date_format_type_of_string($date);
        $dateChange = prev_next_time($date);
        $yearsList = $this->timeline_model->get_years_list();
        $monthsList = months_list();

        $view_data['title'] = 'Biểu đồ đường';
        $view_data['list'] = $this->timeline_model->summary_inout_types_auto($extractedDate['y'], $extractedDate['m']);
        $view_data['list'] = $this->timeline_model->calc_cumulative($view_data['list']);
        $view_data['year'] = $extractedDate['y'] ?? '';
        $view_data['month'] = $extractedDate['m'] ?? '';
        $view_data['select'] = [
            'year' => array_combine($yearsList, $yearsList),
            'month' => array_combine($monthsList, $monthsList),
        ];
        $view_data['url'] = [
            'dateSelectionForm' => $this->base_url([$this->router->fetch_method()]),
            'back' => base_url(),
            'dateChange' => [
                'prev' => $this->base_url([$this->router->fetch_method(), $dateChange[0]]).query_string(),
                'next' => $this->base_url([$this->router->fetch_method(), $dateChange[1]]).query_string(),
            ],
            'timeline' => base_url(['timeline', $this->router->fetch_method(), $date]),
        ];
        $view_data = array_merge($view_data, compact('date', 'dateFormatType'));

        $this->template->write_view('MAIN', 'chart/line', $view_data);
        $this->template->render();
    }

    /**
     * Trang danh sách chi tiết thu chi theo ngày.
     *
     * @param string $date thời gian muốn xem danh sách, có thể nhận format:
     *                     - yyyy-mm-dd
     *                     - yyyy-mm
     *                     - yyyy
     */
    public function pie(string $date = '')
    {
        if (empty($date)) {
            show_error(settings('err_bad_request'));
        }

        if (empty($range = boundary_date($date))) {
            show_error(settings('err_bad_request'));
        }

        // Lấy thông tin từ request parameter
        $inout_type_id = (int) ($this->input->get('inout_type') ?? array_flip(Inout_model::$INOUT_TYPE)['Chi']);
        $only_show_temp_inout = (bool) $this->input->get('only_show_temp_inout');

        $extractedDate = extract_date_string($date);
        $dateFormatType = date_format_type_of_string($date);
        $dateChange = prev_next_time($date);
        $yearsList = $this->timeline_model->get_years_list();
        $monthsList = months_list();
        $daysList = days_list();

        $view_data['title'] = 'Biểu đồ quạt';
        $view_data['list'] = $this->timeline_model->summary_categories(
            $range[0],
            $range[1],
            $inout_type_id,
            $only_show_temp_inout,
        );
        $view_data['year'] = $extractedDate['y'] ?? '';
        $view_data['month'] = $extractedDate['m'] ?? '';
        $view_data['day'] = $extractedDate['d'] ?? '';
        $view_data['total_items'] = count($view_data['list']);
        $view_data['select'] = [
            'inout_types' => $this->inout_type_model->get_select_tag_data(),
            'year' => array_combine($yearsList, $yearsList),
            'month' => array_combine($monthsList, $monthsList),
            'day' => array_combine($daysList, $daysList),
        ];
        $view_data['url'] = [
            'dateSelectionForm' => $this->base_url([$this->router->fetch_method(), '%s']).query_string(),
            'subForm' => $this->base_url([$this->router->fetch_method(), $date]),
            'back' => base_url(),
            'dateChange' => [
                'prev' => $this->base_url([$this->router->fetch_method(), $dateChange[0]]).query_string(),
                'next' => $this->base_url([$this->router->fetch_method(), $dateChange[1]]).query_string(),
            ],
            'timeline' => base_url(['timeline', $this->router->fetch_method(), $date]),
        ];
        $view_data = array_merge($view_data, compact(
            'date',
            'dateFormatType',
            'inout_type_id',
            'only_show_temp_inout',
        ));

        $this->template->write_view('MAIN', 'chart/pie', $view_data);
        $this->template->render();
    }
}
