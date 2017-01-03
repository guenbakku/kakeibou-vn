<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Viewlist_model extends Inout_Model {
    
    /*
     *--------------------------------------------------------------------
     * Lấy danh sách tổng chi tiêu theo ngày (trong một tháng)
     *--------------------------------------------------------------------
     */
    public function summary_inout_types_by_day($year, $month)
    {
        $year = (int)$year;
        $month = (int)$month;
        
        if ($year < 0 || $month < 1 || $month > 12){
            throw new Exception(Constants::ERR_BAD_REQUEST);
        }
        
        $range = array(
            date('Y-m-d', strtotime("{$year}-{$month}-01")),
            date('Y-m-t', strtotime("{$year}-{$month}-01"))
        );
       
        $db_list = $this->summary_inout_types('%Y-%m-%d', $range[0], $range[1]);
        
        $full_list_keys = date_range($range[0], $range[1]);
        
        return $this->combineList($full_list_keys, $db_list);
        
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy danh sách tổng chi tiêu theo tháng (trong một năm)
     *--------------------------------------------------------------------
     */
    public function summary_inout_types_by_month($year=0)
    {
        $year = (int)$year;
        
        if ($year < 0){
            throw new Exception(Constants::ERR_BAD_REQUEST);
        }
        
        $range = array(
            date('Y-01', strtotime("{$year}-01")),
            date('Y-12', strtotime("{$year}-12"))
        );

        $db_list = $this->summary_inout_types('%Y-%m', $range[0], $range[1]);
        
        $full_list_keys = array_map(function($month) use($year){
            return sprintf('%04d-%02d', $year, $month);
        }, range(1, 12));
        
        return $this->combineList($full_list_keys, $db_list);
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy danh sách tổng chi tiêu theo năm
     *--------------------------------------------------------------------
     */
    public function summary_inout_types_by_year()
    {
        $full_list_keys = $this->getYearsList();
                
        $db_list = $this->summary_inout_types('%Y', $full_list_keys[0], $full_list_keys[count($full_list_keys)-1]);
        
        return $this->combineList($full_list_keys, $db_list);
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy dữ liệu thu, chi theo từng ngày trong khoảng thời gian từ from -> to
     *
     * @param   string  : 'yyyy-mm-dd'
     * @param   string  : 'yyyy-mm-dd'
     * @param   int     : id loại tài khoản, nếu là 0 -> tất cả account
     * @param   int     : id người phụ trách, nếu là 0 -> tất cả member
     *--------------------------------------------------------------------
     */
    public function getInoutsOfDay($from, $to, $account, $player)
    {
        $this->load->model('search_model');
        
        $this->search_model->inout_from      = $from;
        $this->search_model->inout_to        = $to;
        $this->search_model->account         = $account;
        $this->search_model->player          = $player;
        $this->search_model->hide_pair_inout = $account == 0? true : false;
        
        return $this->search_model->search(); 
    }
    
    /*
     *--------------------------------------------------------------------
     * Tính số tổng tiền còn lại tính đến hiện tại theo Tiền mặt, Tài khoản, Tổng cộng
     *
     *--------------------------------------------------------------------
     */
    public function getRemaining()
    {
        $now = date('Y-m-d');
        
        $sql = sprintf("SELECT SUM(`amount`) as `future_amount`,
                               SUM(CASE WHEN `date` <= '{$now}' THEN `amount` ELSE 0 END) AS `current_amount`,
                               `inout_records`.`account_id`,
                               `accounts`.`name` as `account`, 
                               `users`.`fullname` as `player`
                        FROM `%s` 
                        JOIN `accounts` ON `accounts`.`id` = `inout_records`.`account_id`
                        JOIN `users` ON `users`.`id` = `inout_records`.`player`
                        GROUP BY `account_id`, `player`
                        ORDER BY `account_id` ASC, `player` ASC", self::TABLE);
                        
        $data = $this->db->query($sql)->result_array(); 
        
        $combine_data = array();
        $total = array(0, 0);
        foreach ($data as $i => $item){
            $total[0] += $item['current_amount'];
            $total[1] += $item['future_amount'];
            
            if ($item['account_id'] == 1){
                $combine_data[$item['player']] = array($item['current_amount'], $item['future_amount']);
            }
            else {
                @$combine_data[$item['account']][0] += $item['current_amount'];
                @$combine_data[$item['account']][1] += $item['future_amount'];
            }
        }
        
        $combine_data['Tổng cộng'] = $total;
        
        return $combine_data;
    }
    
    /*
     *--------------------------------------------------------------------
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
     *--------------------------------------------------------------------
     */
    public function getLiquidOutgoStatus()
    {
        $month_outgo_plans = current($this->setting_model->get('month_outgo_plans', 'value'));
        
        if ($month_outgo_plans < 0){
            return false;
        }
        
        $today = date("Y-m-d");
        $month = date("Y-m");
        
        $sql = "SELECT SUM(`amount`) as `liqid_outgo_to_now`,
                       SUM(CASE WHEN `date` = '{$today}' THEN `amount` ELSE 0 END) AS `liqid_outgo_today`
                FROM `inout_records`
                JOIN `categories` ON `categories`.`id` = `inout_records`.`category_id`
                WHERE DATE_FORMAT(`date`, '%Y-%m') = '{$month}'
                    AND `inout_type_id` = 2
                    AND `month_fixed_money` = 0
                    AND `pair_id` = ''";
                            
        $outgo = $this->db->query($sql)->row_array();
        
        $remaining_days = days_in_month(date('m')) - date('d') + 1;
        
        // Gắn dữ liệu vào vị trí tương ứng và thêm tỷ lệ phần trăm
        return array_map(
            function ($item){
                $item[2] = $item[1]!=0? floor($item[0]/$item[1]*100) : 0;
                return $item;
            }
            , array(
                'today' => array(
                    - $outgo['liqid_outgo_today'],
                    floor(($month_outgo_plans + $outgo['liqid_outgo_to_now'] - $outgo['liqid_outgo_today'])/$remaining_days),
                ),
                'month' => array(
                    - $outgo['liqid_outgo_to_now'],
                    $month_outgo_plans,
                )
            )
        );
    }
    
    /*
     *--------------------------------------------------------------------
     * Tính tổng thu, chi, chênh lệch trong một khoảng thời gian
     *
     * @param   string  : format date dùng trong SQL WHERE & GROUP
     * @param   string  : min date
     * @param   string  : max date
     * @return  array 
     *--------------------------------------------------------------------
     */
    public function summary_inout_types($date_format_string, $min_date, $max_date)
    {
        $sql = "SELECT `date`, `thu`, `chi`, (`thu` + `chi`) AS `tong`
                FROM (
                        SELECT DATE_FORMAT(`date`, '{$date_format_string}') as `date`, 
                               SUM(CASE WHEN `categories`.`inout_type_id` = 1 THEN `amount` ELSE 0 END) AS `thu`, 
                               SUM(CASE WHEN `categories`.`inout_type_id` = 2 THEN `amount` ELSE 0 END) AS `chi`
                        FROM `inout_records`
                        JOIN `categories` ON `categories`.`id` = `inout_records`.`category_id` 
                        WHERE DATE_FORMAT(`date`, '{$date_format_string}') >= '{$min_date}' 
                              AND DATE_FORMAT(`date`, '{$date_format_string}') <= '{$max_date}' 
                              AND `pair_id` = ''
                        GROUP BY DATE_FORMAT(`date`, '{$date_format_string}')
                     ) AS t
                ORDER BY `date` ASC";
        
        return $this->db->query($sql)->result_array(); 
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy danh sách tất cả năm có trong table inout_record
     * 
     * @param   void
     * @return  array
     *--------------------------------------------------------------------
     */
    public function getYearsList()
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
    
    /*
     *--------------------------------------------------------------------
     * Tính ngày giới hạn khi xem danh sách thu chi theo ngày
     *
     * @param   string  : chuỗi quy định thời gian: yyyy-mm-dd, yyyy-mm, yyyy
     * @return  array   : ngày đầu và cuối của khoảng thời gian đó
     *--------------------------------------------------------------------
     */
    public function getBoundaryDate($time)
    {
        $mdate = str_replace('-', '', $time);
        if (preg_match('/^\d{8}$/', $mdate)){
            return array(
                date('Y-m-d', strtotime($mdate)), 
                date('Y-m-d', strtotime($mdate)), 
            );
        }
        elseif (preg_match('/^\d{6}$/', $mdate)){
            return array(
                date('Y-m-d', strtotime("{$mdate}01")),
                date('Y-m-t', strtotime("{$mdate}01")),
            );
        }
        elseif (preg_match('/^\d{4}$/', $mdate)){
            return array(
                date('Y-m-d', strtotime($mdate.'0101')),
                date('Y-m-d', strtotime($mdate.'1231')),
            );
        }
        else {
            return false;
        }
    }
    
    /*
     *--------------------------------------------------------------------
     * Tính ngày, tháng hoặc năm trước và sau của dữ liệu nhập vào
     * Nếu dữ liệu nhập vào là dạng ngày  -> ngày trước và sau
     *                              tháng -> tháng trước và sau
     *                              năm   -> năm trước và sau  
     *--------------------------------------------------------------------
     */
    public function getPrevNextTime($time)
    {
        $mdate = str_replace('-', '', $time);
        if (preg_match('/^\d{8}$/', $mdate)){
            return array(
                date('Y-m-d', strtotime($mdate . ' -1 day')), 
                date('Y-m-d', strtotime($mdate . ' +1 day')), 
            );
        }
        elseif (preg_match('/^\d{6}$/', $mdate)){
            return array(
                date('Y-m', strtotime("{$mdate}01" . ' -1 month')),
                date('Y-m', strtotime("{$mdate}01" . ' +1 month')),
            );
        }
        elseif (preg_match('/^\d{4}$/', $mdate)){
            return array(
                date('Y', strtotime($mdate.'0101' . ' -1 year')),
                date('Y', strtotime($mdate.'1231' . ' +1 year')),
            );
        }
        else {
            return false;
        }
    }
    
    /*
     *--------------------------------------------------------------------
     * Gắn từng item từ List (lấy từ CSDL) vào danh sách thời gian đầy đủ
     *
     * @param   array   : danh sách thời gian đầy đủ
     * @param   array   : list lấy từ CSDL
     * @return  array   : full list sau khi gắn dữ liệu
     *--------------------------------------------------------------------
     */
    private function combineList($full_list_keys=array(), $db_list=array())
    {
        $empty_item = array('tong' => 0, 'thu' => 0, 'chi' => 0, 'date' => null);
        $full_list = array();
        
        foreach ($full_list_keys as $k) {
            $db_item = current($db_list);
            
            if ($k == $db_item['date']){
                $item = $db_item;
                next($db_list);
            }
            else {
                $item = $empty_item;
            }
            
            $full_list[] = array_merge($item, array('date' => $k));
        }
        
        return $full_list;
    }
    
}