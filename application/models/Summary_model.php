<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Summary_model extends Inout_Model {
    
    public function getListByDay($year, $month)
    {
        $range = array(
            date('Y-m-d', strtotime("{$year}-{$month}-01")),
            date('Y-m-t', strtotime("{$year}-{$month}-01"))
        );
       
        $db_list = $this->getSumListFromDB('%Y-%m-%d', $range[0], $range[1]);
        
        $full_list_keys = date_range($range[0], $range[1]);
        
        return $this->combineList($full_list_keys, $db_list);
        
    }
    
    public function getListByMonth($year)
    {
        $range = array(
            date('Y-01', strtotime("{$year}-01")),
            date('Y-12', strtotime("{$year}-12"))
        );

        $db_list = $this->getSumListFromDB('%Y-%m', $range[0], $range[1]);
        
        $full_list_keys = array_map(function($month) use($year){
            return sprintf('%04d-%02d', $year, $month);
        }, range(1, 12));
        
        return $this->combineList($full_list_keys, $db_list);
    }
    
    public function getListByYear()
    {
        $full_list_keys = self::getYearsListInDB();
                
        $db_list = $this->getSumListFromDB('%Y', $full_list_keys[0], $full_list_keys[count($full_list_keys)-1]);
        
        return array_reverse($this->combineList($full_list_keys, $db_list), true);
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy dữ liệu thu, chi theo từng ngày trong khoảng thời gian từ from -> to
     *
     * @param   string  : 'yyyy-mm-dd'
     * @param   string  : 'yyyy-mm-dd'
     * @param   int     
     * @param   int     : số quy định trong dữ liệu, nếu là 0 -> tất cả account
     *--------------------------------------------------------------------
     */
    public function getDailyList($from, $to, $account, $player)
    {
        $sql['SELECT']   = "SELECT `inout_records`.`iorid`,
                                   `inout_records`.`amount`,
                                   `inout_records`.`memo`,
                                   `inout_records`.`date`,
                                   `inout_types`.`name` as `inout_type`,
                                   `accounts`.`name` as `account`, 
                                   `categories`.`name` as `category`,
                                   `users`.`fullname` as `player`,
                                   `users`.`label` as `player_label` ";
        $sql['FROM']     = "FROM `inout_records` ";
        $sql['JOINT']    = "JOIN `accounts` ON `accounts`.`aid` = `inout_records`.`account_id`
                            JOIN `inout_types` ON `inout_types`.`iotid` = `inout_records`.`inout_type_id`
                            JOIN `categories` ON `categories`.`cid` = `inout_records`.`category_id`
                            JOIN `users` ON `users`.`uid` = `inout_records`.`player` ";
        $sql['WHERE']    = "WHERE `inout_records`.`date` >= '{$from}' 
                              AND `inout_records`.`date` <= '{$to}' ";
        $sql['ORDER_BY'] = "ORDER BY `inout_records`.`date` ASC, 
                                     `inout_records`.`inout_type_id` ASC, 
                                     `inout_records`.`created_on` ASC ";
        
        if ($account > 0){
            $sql['WHERE'] .= "AND `inout_records`.`account_id` = '{$account}' ";
        }
        else {
            $sql['WHERE'] .= "AND `pair_id` = ''";
        }
        
        if ($player > 0){
            $sql['WHERE'] .= "AND `inout_records`.`player` = '{$player}' " ;
        }
        
        return $this->db->query(implode(' ', $sql))->result_array(); 
    }
    
    /*
     *--------------------------------------------------------------------
     * Tính số tổng tiền còn lại tính đến hiện tại theo Tiền mặt, Tài khoản, Tổng cộng
     *
     *--------------------------------------------------------------------
     */
    public function getRemaining()
    {
        $sql = sprintf("SELECT SUM(`inout_records`.`amount`) as `amount`,
                               `inout_records`.`account_id`,
                               `accounts`.`name` as `account`, 
                               `users`.`fullname` as `player`
                        FROM `%s` 
                        JOIN `accounts` ON `accounts`.`aid` = `inout_records`.`account_id`
                        JOIN `users` ON `users`.`uid` = `inout_records`.`player`
                        GROUP BY `account_id`, `player`
                        ORDER BY `account_id` ASC, `player` ASC", self::TABLE);
                        
        $data = $this->db->query($sql)->result_array()  ; 
        
        $combine_data = array();
        $total = 0;
        foreach ($data as $i => $item){
            $total += $item['amount'];
            if ($item['account_id'] == 1){
                $combine_data[$item['player']] = $item['amount'];
            }
            else {
                @$combine_data[$item['account']] += $item['amount'];
            }
        }
        
        $combine_data['Tổng cộng'] = $total;
        
        return $combine_data;
    }
    
    public function getDayAvailableOutgo($currentOutgo)
    {
        $month_outgo_plans = current($this->setting_model->get('month_outgo_plans', 'value'));
        $remaining_days = days_in_month(date('m')) - date('d') + 1;
        return floor(($month_outgo_plans - $currentOutgo)/$remaining_days);
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy danh sách tổng, thu, chi trong một khoảng thời gian
     *
     * @param   string  : format date dùng trong SQL WHERE & GROUP
     * @param   string  : min date
     * @param   string  : max date
     * @return  array 
     *--------------------------------------------------------------------
     */
    public function getSumListFromDB($date_format_string, $min_date, $max_date)
    {
        $sql = "SELECT `key`, `thu`, `chi`, (`thu` + `chi`) AS `tong`
                FROM (
                        SELECT DATE_FORMAT(`date`, '{$date_format_string}') as `key`, 
                               SUM(CASE WHEN `inout_type_id` = 1 THEN `amount` ELSE 0 END) AS `thu`, 
                               SUM(CASE WHEN `inout_type_id` = 2 THEN `amount` ELSE 0 END) AS `chi`
                        FROM `inout_records`
                        WHERE DATE_FORMAT(`date`, '{$date_format_string}') >= '{$min_date}' 
                              AND DATE_FORMAT(`date`, '{$date_format_string}') <= '{$max_date}' 
                              AND `pair_id` = ''
                        GROUP BY DATE_FORMAT(`date`, '{$date_format_string}')
                     ) AS t";
        
        return $this->db->query($sql)->result_array(); 
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
        $empty_item = array('tong' => 0, 'thu' => 0, 'chi' => 0, 'empty' => true);
        $full_list = array();
        
        foreach ($full_list_keys as $k){
            $item = current($db_list);
            if ($k == $item['key']){
                unset($item['key']);
                $item['empty'] = false;
                $full_list[$k] = $item;
                next($db_list);
            }
            else {
                $full_list[$k] = $empty_item;
            }
        }
        
        return $full_list;
    }
    
}