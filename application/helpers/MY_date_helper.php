<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 *--------------------------------------------------------------------
 * Tính thứ của một ngày cụ thể
 *
 * @param int/string : timestamp hoặc string theo format datetime
 * @return string    : thứ trong tuần
 *--------------------------------------------------------------------
 */
function day_of_week($date)
{
    $days = array('Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy', 'Chủ Nhật');
    
    if (!is_numeric($date)){
        $date = strtotime($date);
    }
    
    if ($date === false){
        return false;
    }
    
    $day_index = date('N', $date) - 1;
    
    return $days[$day_index];
}