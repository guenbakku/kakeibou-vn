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
 * @param   bool: có lấy cả những item null không
 * @param   array
 *--------------------------------------------------------------------
 */
function extract_date_string(?string $date, bool $includeNullItem = true): array
{
    $date = preg_replace('/[^\d]+/', '-', $date);
    @list($year, $month, $day) = explode('-', $date);
    
    $extracted = array('y' => null, 'm' => null, 'd' => null);
    if (!preg_match('/^\d{1,4}$/', $year)) {
        goto OUTPUT;
    }
    
    $extracted['y'] = sprintf('%04d', $year);
    if (!preg_match('/^\d{1,2}$/', $month)) {
        goto OUTPUT;
    }
    
    $extracted['m'] = sprintf('%02d', $month);
    if (!preg_match('/^\d{1,2}$/', $day)) {
        goto OUTPUT;
    }
    
    $extracted['d'] = sprintf('%02d', $day);
    
    OUTPUT:
    return $includeNullItem
           ? $extracted
           : array_filter($extracted, function($item){return $item !== null;});
}

/*
 *--------------------------------------------------------------------
 * Tạo chuỗi date từ array 
 * Có thể xem đây là hàm ngược của hàm extract_date_string()
 *
 * @param   array: array chứa year, month, day
 * @param   string: chuỗi để nối
 * return   string: chuỗi date đã nối
 *--------------------------------------------------------------------
 */
function combine_date_string(array $date, string $glue = '-'): ?string
{
    $date = array_slice($date, 0, 3);
    return implode($glue, array_filter($date, function($item){return $item !== null;}));
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
function boundary_date(?string $year, int $month = null, int $day = null): array
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

/*
 *--------------------------------------------------------------------
 * Tính ngày, tháng hoặc năm trước và sau của dữ liệu nhập vào
 * Nếu dữ liệu nhập vào là dạng ngày  -> ngày trước và sau
 *                              tháng -> tháng trước và sau
 *                              năm   -> năm trước và sau  
 *--------------------------------------------------------------------
 */
function prev_next_time(?string $date): array
{
    $extracted = extract_date_string($date, false);
    
    switch (count($extracted)) {
        case 1:
            return array(
                $extracted['y'] - 1,
                $extracted['y'] + 1,
            );
        case 2:
            return array(
                date('Y-m', strtotime(implode('-', $extracted).'-01' . ' -1 month')),
                date('Y-m', strtotime(implode('-', $extracted).'-01' . ' +1 month')),
            );
        case 3:
            return array(
                date('Y-m-d', strtotime(implode('-', $extracted). ' -1 day')),
                date('Y-m-d', strtotime(implode('-', $extracted). ' +1 day')),
            );
        default:
            return array(null, null);
    }
}

function months_list(): array
{
    return array_map(
        function($item){return sprintf('%02d', $item);}, 
        range(1, 12)
    );
}

function days_list(): array
{
    return array_map(
        function($item){return sprintf('%02d', $item);}, 
        range(1, 31)
    );
}