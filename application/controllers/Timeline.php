<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Timeline extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('timeline_model');
    }

    public function index()
    {
        return redirect($this->base_url(['summary', date('Y-m')]));
    }

    /**
     * Trang tổng kết số tiền thu chi theo
     *  - ngày trong tháng,
     *  - tháng trong năm
     *  - năm.
     *
     * @param string $date thời gian muốn xem danh sách, có thể nhận format:
     *                     - yyyy-mm
     *                     - yyyy
     *                     - null
     */
    public function summary(string $date = '')
    {
        $extractedDate = extract_date_string($date);
        $dateFormatType = date_format_type_of_string($date);
        $dateChange = prev_next_time($date);
        $yearsList = $this->timeline_model->get_years_list();
        $monthsList = months_list();

        $view_data['title'] = 'Danh sách tóm tắt';
        $view_data['list'] = $this->timeline_model->summary_inout_types_auto(
            $extractedDate['y'],
            $extractedDate['m'],
            SORT_DESC
        );
        $view_data['year'] = $extractedDate['y'] ?? '';
        $view_data['month'] = $extractedDate['m'] ?? '';
        $view_data['select'] = [
            'year' => array_combine($yearsList, $yearsList),
            'month' => array_combine($monthsList, $monthsList),
        ];
        $view_data['url'] = [
            'dateSelectionForm' => $this->base_url($this->router->fetch_method()),
            'back' => base_url(),
            'dateChange' => [
                'prev' => $this->base_url([$this->router->fetch_method(), $dateChange[0]]).query_string(),
                'next' => $this->base_url([$this->router->fetch_method(), $dateChange[1]]).query_string(),
            ],
            'viewchart' => base_url(['chart', $this->router->fetch_method(), $date]),
            'detailTemplate' => $this->base_url(['detail', '%s']),
        ];
        $view_data['pageScrollTarget'] = $this->_page_scroll_target($date);
        $view_data = array_merge($view_data, compact('date', 'dateFormatType'));

        $this->template->write_view('MAIN', 'timeline/summary', $view_data);
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
    public function detail(string $date = '')
    {
        if (empty($date)) {
            show_error(settings('err_bad_request'));
        }
        if (empty($range = boundary_date($date))) {
            show_error(settings('err_bad_request'));
        }

        // Lấy thông tin từ request parameter
        $offset = $this->input->get('offset') ?? 0;
        $account_id = $this->input->get('account') ?? 0;
        $player_id = $this->input->get('player') ?? 0;
        $inout_type_id = $this->input->get('inout_type') ?? array_flip(Inout_model::$INOUT_TYPE)['Chi'];
        $only_show_temp_inout = $this->input->get('only_show_temp_inout') ?? 0;

        $extractedDate = extract_date_string($date);
        $dateFormatType = date_format_type_of_string($date);
        $dateChange = prev_next_time($date);
        $yearsList = $this->timeline_model->get_years_list();
        $monthsList = months_list();
        $daysList = days_list();

        $view_data['title'] = 'Danh sách chi tiết';

        $this->load->model('search_model');
        $this->search_model->inout_from = $range[0];
        $this->search_model->inout_to = $range[1];
        $this->search_model->account = $account_id;
        $this->search_model->player = $player_id;
        $this->search_model->only_show_temp_inout = $only_show_temp_inout;
        $this->search_model->also_show_pair_inout = $account_id != 0;
        $this->search_model->offset = $offset;
        $view_data['result'] = $this->search_model->search();

        $view_data['year'] = $extractedDate['y'] ?? '';
        $view_data['month'] = $extractedDate['m'] ?? '';
        $view_data['day'] = $extractedDate['d'] ?? '';
        $view_data['current_num'] = count($view_data['result']);
        $view_data['select'] = [
            'accounts' => $this->account_model->get_select_tag_data(),
            'players' => $this->user_model->get_select_tag_data(),
            'inout_types' => $this->inout_type_model->get_select_tag_data(),
            'year' => array_combine($yearsList, $yearsList),
            'month' => array_combine($monthsList, $monthsList),
            'day' => array_combine($daysList, $daysList),
        ];
        $view_data['url'] = [
            'dateSelectionForm' => $this->base_url([$this->router->fetch_method(), '%s']).query_string(),
            'subForm' => $this->base_url([$this->router->fetch_method(), $date]),
            'back' => base_url(),
            'editTemplate' => base_url(['inout', 'edit', '%s']),
            'dateChange' => [
                'prev' => $this->base_url([$this->router->fetch_method(), $dateChange[0]]).query_string(),
                'next' => $this->base_url([$this->router->fetch_method(), $dateChange[1]]).query_string(),
            ],
            'viewchart' => base_url(['chart', $this->router->fetch_method(), $date]),
            'next_page' => $this->search_model->next_page_url(),
        ];
        $view_data = array_merge($view_data, compact(
            'date',
            'dateFormatType',
            'account_id',
            'player_id',
            'inout_type_id',
            'only_show_temp_inout'
        ));

        $this->template->write_view('MAIN', 'timeline/detail', $view_data);
        $this->template->render();
    }

    /**
     * Tạo page-scroll target đến ngày/tháng/năm hiện tại tùy vào kiểu danh sách.
     *
     * @param string $date thời điểm hiện tại để scroll đến
     */
    protected function _page_scroll_target(string $date): string
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
