<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Summary_model extends Inout_Model {
    
    public function getListByDay($year, $month)
    {
        $range = array(
            date('Y-m-d', strtotime("{$year}-{$month}-01")),
            date('Y-m-t', strtotime("{$year}-{$month}-01"))
        );
       
        $db_list = $this->getListFromDB('%Y-%m-%d', $range[0], $range[1]);
        
        $full_list_keys = date_range($range[0], $range[1]);
        
        return $this->combineList($full_list_keys, $db_list);
        
    }
    
    public function getListByMonth($year)
    {
        $range = array(
            date('Y-01', strtotime("{$year}-01")),
            date('Y-12', strtotime("{$year}-12"))
        );

        $db_list = $this->getListFromDB('%Y-%m', $range[0], $range[1]);
        
        $full_list_keys = array_map(function($month) use($year){
            return sprintf('%04d-%02d', $year, $month);
        }, range(1, 12));
        
        return $this->combineList($full_list_keys, $db_list);
    }
    
    public function getListByYear()
    {
        $full_list_keys = self::getYearsListInDB();
                
        $db_list = $this->getListFromDB('%Y', $full_list_keys[0], $full_list_keys[count($full_list_keys)-1]);
        
        return array_reverse($this->combineList($full_list_keys, $db_list), true);
    }
    
    private function getListFromDB($date_format_string, $min_date, $max_date)
    {
        $sql = "SELECT `key`, `thu`, `chi`, (`thu` - `chi`) AS `tong`
                FROM (
                        SELECT DATE_FORMAT(`date`, '{$date_format_string}') as `key`, 
                               SUM(CASE WHEN `inout_type_id` = 1 THEN `amount` ELSE 0 END) AS `thu`, 
                               SUM(CASE WHEN `inout_type_id` = 2 THEN `amount` ELSE 0 END) AS `chi`
                        FROM `inout_records`
                        WHERE DATE_FORMAT(`date`, '{$date_format_string}') >= '{$min_date}' 
                              AND DATE_FORMAT(`date`, '{$date_format_string}') <= '{$max_date}' AND `pair_id` = ''
                        GROUP BY DATE_FORMAT(`date`, '{$date_format_string}')
                     ) AS t";
        
        return $this->db->query($sql)->result_array(); 
    }
    
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