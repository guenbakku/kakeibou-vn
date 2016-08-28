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
    $days = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
    
    if (!is_numeric($date)){
        $date = strtotime($date);
    }
    
    if ($date === false){
        return false;
    }
        
    return $days[date('w', $date)];
}