<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Timeline_model extends Inout_Model {

    /**
     * Dựa vào year, month được truyền vào để tự động lựa method
     * lấy danh sách tổng chi tiêu thích hợp
     */
    public function summary_inout_types_auto(?int $year, ?int $month, int $sort_order = SORT_ASC): array
    {
        if ($year !== null && $month !== null) {
            $list = $this->summary_inout_types_by_day_in_month($year, $month);
        }
        else if ($year !== null) {
            $list = $this->summary_inout_types_by_month_in_year($year);
        }
        else {
            $list = $this->summary_inout_types_by_year();
        }

        // Sort danh sách theo date
        $date = array_column($list, 'date');
        array_multisort($date, $sort_order, $list);

        return $list;
    }

    /**
     * Lấy danh sách tổng chi tiêu theo ngày (trong một tháng)
     */
    public function summary_inout_types_by_day_in_month(int $year, int $month): array
    {
        $range = boundary_date($year, $month);
        $db_list = $this->summary_inout_types($range[0], $range[1], '%Y-%m-%d');
        $full_list_keys = date_range($range[0], $range[1]);

        return $this->combine_list($full_list_keys, $db_list);
    }

    /**
     * Lấy danh sách tổng chi tiêu theo tháng (trong một năm)
     */
    public function summary_inout_types_by_month_in_year(int $year): array
    {
        $range = boundary_date($year);
        $db_list = $this->summary_inout_types($range[0], $range[1], '%Y-%m');
        $full_list_keys = array_map(function($month) use($year){
            return sprintf('%04d-%02d', $year, $month);
        }, range(1, 12));

        return $this->combine_list($full_list_keys, $db_list);
    }

    /**
     * Lấy danh sách tổng chi tiêu theo năm
     */
    public function summary_inout_types_by_year(): array
    {
        $full_list_keys = $this->get_years_list();
        $range = array(
            reset($full_list_keys).'-01-01',
            end($full_list_keys).'-12-31'
        );
        $db_list = $this->summary_inout_types($range[0], $range[1], '%Y');

        return $this->combine_list($full_list_keys, $db_list);
    }

    /**
     * Tính tổng thu, chi, chênh lệch trong một khoảng thời gian.
     * Không tính các inout lưu động nội bộ.
     *
     * @param   string  : format date dùng trong SQL WHERE & GROUP
     * @param   string  : min date
     * @param   string  : max date
     * @return  array
     */
    public function summary_inout_types(string $from, string $to, string $date_format_string): array
    {
        $subQuery = $this->db->select("DATE_FORMAT(`date`, '{$date_format_string}') as `date`,
                                       SUM(CASE WHEN `categories`.`inout_type_id` = 1 THEN `amount` ELSE 0 END) AS `thu`,
                                       SUM(CASE WHEN `categories`.`inout_type_id` = 2 THEN `amount` ELSE 0 END) AS `chi`")
                             ->from('inout_records')
                             ->join('categories', 'categories.id = inout_records.category_id')
                             ->where('inout_records.date >=', $from)
                             ->where('inout_records.date <=', $to)
                             ->where('inout_records.pair_id', '')
                             ->group_by("DATE_FORMAT(`inout_records`.`date`, '{$date_format_string}')")
                             ->get_compiled_select();

        return $this->db->select('date, thu, chi, (`thu` + `chi`) AS `tong`')
                        ->from("($subQuery) t")
                        ->order_by('date ASC')
                        ->get()->result_array();
    }

    /**
     * Tính lũy kế của thu, chi, tổng trong một dãi thời gian
     *
     * @param   array: dữ liệu thu, chi, tổng theo một dãi thời gian
     * @return  array
     */
    public function calc_cumulative(array $timeline): array
    {
        foreach ($timeline as $i => &$item) {
            if ($i == 0) {
                continue;
            } else {
                $preItem = $timeline[$i-1];
            }
            foreach (['tong', 'thu', 'chi'] as $key) {
                $item[$key] += $preItem[$key];
            }
        }
        return $timeline;
    }

    /**
     * Tính số tổng tiền còn lại tính đến hiện tại theo Tiền mặt, Tài khoản, Tổng cộng
     */
    public function get_remaining(): array
    {
        $now = date('Y-m-d');

        $sql = sprintf("SELECT SUM(`amount`) as `future_amount`,
                               SUM(CASE WHEN `date` <= '{$now}' THEN `amount` ELSE 0 END) AS `current_amount`,
                               `accounts`.`id` as `account_id`,
                               `accounts`.`name` as `account`,
                               `users`.`fullname` as `player`
                        FROM `%s`
                        JOIN `users` ON `users`.`id` = `inout_records`.`player`
                        RIGHT JOIN `accounts` ON `accounts`.`id` = `inout_records`.`account_id`
                        GROUP BY `account`, `player`
                        ORDER BY `order_no` ASC, `account_id` ASC, `player` ASC", self::TABLE);

        $data = $this->db->query($sql)->result_array();

        $combine_data = array();
        $total = array(0, 0);
        foreach ($data as $i => $item){
            $total[0] += $item['current_amount'];
            $total[1] += $item['future_amount'];

            if ($item['account_id'] == 1){
                if (!empty($item['player'])) {
                    $combine_data[$item['player']] = array($item['current_amount'], $item['future_amount']);
                }
            }
            else {
                @$combine_data[$item['account']][0] += $item['current_amount'];
                @$combine_data[$item['account']][1] += $item['future_amount'];
            }
        }

        $combine_data['Tổng cộng'] = $total;

        // Loại account có tiền còn lại bằng 0 ra khỏi danh sách dữ liệu
        $combine_data = array_filter($combine_data, function($item, $key) {
            return $key === 'Tổng cộng' || $item[0] != 0 || $item[1] != 0;
        }, ARRAY_FILTER_USE_BOTH);

        return $combine_data;
    }

    /**
     * Tính: số chi lưu động của ngày hôm nay tính tới thời điểm hiện tại
     *       số chi lưu động của tháng này tính tới thời điểm hiện tại
     *       số tiền trung bình có thể chi mỗi ngày từ đây đến cuối tháng
     *       tổng số tiền dự tính chi trong tháng này (lấy từ CSDL)
     *
     * @param   void
     * @return  array: array(
     *                        'today' => array(
     *                                          0 -> số chi lưu động của ngày hôm nay
     *                                          1 -> số tiền trung bình có thể chi mỗi ngày từ đây đến cuối tháng
     *                                          2 -> tỷ lệ phần trăm
     *                                         ),
     *                        'month' => array(
     *                                          0 -> số chi lưu động của tháng này (tới thời điểm hiện tại)
     *                                          1 -> số tiền dự định chi trong tháng
     *                                          2 => tỷ lệ phần trăm
     *                                         ),
     *                       )
     */
    public function get_liquid_outgo_status(): array
    {
        $month_estimated_outgo = $this->category_model->get_month_estimated_outgo()['liquid'];

        if ($month_estimated_outgo < 0){
            return false;
        }

        $today = date("Y-m-d");
        $month = date("Y-m");

        $sql = "SELECT SUM(`amount`) as `liqid_outgo_to_now`,
                       SUM(CASE WHEN `date` = '{$today}' THEN `amount` ELSE 0 END) AS `liqid_outgo_today`
                FROM `inout_records`
                JOIN `categories` ON `categories`.`id` = `inout_records`.`category_id`
                WHERE DATE_FORMAT(`inout_records`.`date`, '%Y-%m') = '{$month}'
                    AND `categories`.`inout_type_id` = 2
                    AND `inout_records`.`skip_month_estimated` = 0
                    AND `inout_records`.`pair_id` = ''";

        $outgo = $this->db->query($sql)->row_array();

        $remaining_days = days_in_month(date('m')) - date('d') + 1;

        // Thông tin sẽ gửi trả về
        $result = array(
            'today' => array(
                'elapsed'   => - $outgo['liqid_outgo_today'],
                'estimated' => floor(($month_estimated_outgo + $outgo['liqid_outgo_to_now'] - $outgo['liqid_outgo_today'])/$remaining_days),
                'remain'    => null,
                'elapsed_percent'   => null,
                'remain_percent'    => null,
                'estimated_percent' => 100,
            ),
            'month' => array(
                'elapsed'   => - $outgo['liqid_outgo_to_now'],
                'estimated' => $month_estimated_outgo,
                'remain'    => null,
                'elapsed_percent'   => null,
                'remain_percent'    => null,
                'estimated_percent' => 100,
            )
        );

        // Gắn dữ liệu vào vị trí tương ứng và thêm tỷ lệ phần trăm
        return array_map(
            function ($item){
                $remain = $item['estimated'] - $item['elapsed'];
                $item['remain'] = $remain > 0? $remain : 0;
                $item['elapsed_percent'] = $item['estimated'] != 0? floor($item['elapsed']/$item['estimated']*100) : 0;
                $item['remain_percent'] = $item['estimated'] != 0? floor($item['remain']/$item['estimated']*100) : 0;
                return $item;
            }, $result
        );
    }

    /**
     * Tính tổng thu chi theo từng category
     * Không tính các category được set restrict_delete
     *
     * @param   string  : 'yyyy-mm-dd'
     * @param   string  : 'yyyy-mm-dd'
     * @param   int     : của inout_type
     * @return  array
     */
    public function summary_categories(string $from, string $to, int $inout_type_id): array
    {
        $subQuery = $this->db->select('categories.id AS category_id,
                                         ABS(SUM(`inout_records`.`amount`)) AS total')
                               ->from('inout_records')
                               ->where('categories.inout_type_id', $inout_type_id)
                               ->where('categories.restrict_delete != ', 1)
                               ->where('inout_records.date >=', $from)
                               ->where('inout_records.date <=', $to)
                               ->join('categories', 'categories.id = inout_records.category_id')
                               ->group_by('categories.id', 'categories.name')
                               ->get_compiled_select();

        return $this->db->select('categories.id AS category_id,
                                  categories.name AS category_name,
                                  IFNULL(t1.total, 0 ) AS total')
                        ->from("($subQuery) t1")
                        ->where('categories.inout_type_id', $inout_type_id)
                        ->where('categories.restrict_delete != ', 1)
                        ->join('categories', 'categories.id = t1.category_id', 'right outer')
                        ->order_by('categories.order_no')
                        ->get()->result_array();

    }

    /**
     * Lấy danh sách tất cả năm có trong table inout_record
     *
     * @param   void
     * @return  array
     */
    public function get_years_list(): array
    {
        $table = 'inout_records';
        $range = $this->db->select("DATE_FORMAT(MIN(`date`), '%Y') as `min`,
                                    DATE_FORMAT(MAX(`date`), '%Y') as `max`", false)
                          ->get($table)->row_array();

        // Thêm năm hiện tại nếu max_year nhỏ hơn năm hiện tại
        $thisYear = date('Y');
        if ($range['max'] < $thisYear) {
            $range['max'] = $thisYear;
        }

        $full_list = array_map(function($year){
            return sprintf('%04d', $year);
        }, range($range['min'], $range['max']));

        return $full_list;
    }

    /**
     * Gắn từng item từ list (lấy từ CSDL) vào danh sách thời gian đầy đủ
     *
     * @param   array   : danh sách thời gian đầy đủ
     * @param   array   : list lấy từ CSDL
     * @return  array   : full list sau khi gắn dữ liệu
     */
    private function combine_list($full_list_keys = [], $db_list = [])
    {
        $empty_item = ['tong' => 0, 'thu' => 0, 'chi' => 0, 'date' => null];
        $full_list = [];

        foreach ($full_list_keys as $k) {
            $db_item = current($db_list);

            if ($k == $db_item['date']){
                $item = $db_item;
                next($db_list);
            }
            else {
                $item = $empty_item;
            }

            $full_list[] = array_merge($item, ['date' => $k]);
        }

        return $full_list;
    }
}
