<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 *--------------------------------------------------------------------
 * Tính thứ của một ngày cụ thể
 *
 * @param int/string : timestamp hoặc string theo format datetime
 * @return string    : thứ trong tuần
 *--------------------------------------------------------------------
 */
function day_of_week($date): ?string
{
    $days = array('Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy');
    
    if (!is_numeric($date)){
        $date = strtotime($date);
    }
    
    if ($date === false){
        return null;
    }
        
    return $days[date('w', $date)];
}

/*
 *--------------------------------------------------------------------
 * Tách chuỗi date thành array('y' => yyyy, 'm' => mm, 'd' => đd)
 *
 * @param   string: bắt buộc phải có ký tự ngăn cách giữa yyyy-mm-dd
 * @param   array
 *--------------------------------------------------------------------
 */
function extract_date_string($date): array
{
    $date = preg_replace('/[^\d]+/', '-', $date);
    @list($year, $month, $day) = explode('-', $date);
    
    $extracted = array('y' => null, 'm' => null, 'd' => null);
    if (!preg_match('/^\d{1,4}$/', $year)) {
        return $extracted;
    }
    
    $extracted['y'] = sprintf('%04d', $year);
    if (!preg_match('/^\d{1,2}$/', $month)) {
        return $extracted;
    }
    
    $extracted['m'] = sprintf('%02d', $month);
    if (!preg_match('/^\d{1,2}$/', $day)) {
        return $extracted;
    }
    
    $extracted['d'] = sprintf('%02d', $day);
    return $extracted;
}

/*
 *--------------------------------------------------------------------
 * Tính ngày giới hạn (bắt đầu và kết thúc) của một khoảng thời gian
 * Ví dụ 
 *      1. 2016         -> array('2016-01-01', '2016-12-31')
 *      2. 2016-12      -> array('2016-12-01', '2016-12-31')
 *      3. 2016-12-31   -> array('2016-12-31', '2016-12-31')
 *
 * @param   int : year
 * @param   int : month
 * @param   int : day
 * @return  array : ngày đầu và cuối của khoảng thời gian đó
 *--------------------------------------------------------------------
 */
function boundary_date(string $year, int $month = null, int $day = null): array
{
    // Tách parameter đầu tiên thành year, month, day 
    // nếu parameter đầu tiên là chuỗi format kiểu date
    if (!is_numeric($year) && is_string($year)) {
        $extracted = extract_date_string($year);
        list('y' => $year, 'm' => $month, 'd' => $day) = $extracted;
    }
    
    if (!is_numeric($year)) {
        return array();
    }
    
    $range = array(
        date('Y-m-d', strtotime($year.'-01-01')),
        date('Y-m-d', strtotime($year.'-12-31')),
    );
    if (!is_numeric($month)) {
        return $range;
    }
    
    $range = array(
        date('Y-m-d', strtotime($year.'-'.$month.'-01')),
        date('Y-m-t', strtotime($year.'-'.$month.'-01')),
    );
    if (!is_numeric($day)) {
        return $range;
    }
    
    $range = array(
        date('Y-m-d', strtotime($year.'-'.$month.'-'.$day)),
        date('Y-m-d', strtotime($year.'-'.$month.'-'.$day)),
    );
    return $range;
}