<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trả về array mới có index là giá trị của một column cụ thể có trong array
 *
 * @param   array  : array có ít nhất 2 chiều
 * @paran   string : tên một trong những column trong array. Index của array mới sẽ được tạo
 *                   từ các giá trị trong column này
 * @param   string : tên một trong những column trong array.
 *                   Nếu biến này được quy định, array sẽ được tạo với dạng array(key => value)
 *                      với value là giá trị trong cột được quy định ở biến này
 *                   Nếu biến này là null, array sẽ được tạo với dạng array(key => item)
 *                      với item là item nguyên bản của array truyền vào
 * @return  array  : array mới đã được đánh index
 */
function array_gen_key($arr=null, $key=null, $val=null){

    if (!is_array($arr))
        return false;

    // Kiểm tra sự tồn tại của colum trong array
    $first_item = reset($arr);
    if (!isset($first_item[$key]) || !is_string($first_item[$key])) {
        return $arr;
    }
    if ($val !== null && !isset($first_item[$val])) {
        return $arr;
    }

    $new_arr = array();
    if ($val === null) {
        foreach ($arr as $i => $item) {
            $new_arr[$item[$key]] = $item;
        }
    }
    else {
        foreach ($arr as $i => $item) {
            $new_arr[$item[$key]] = $item[$val];
        }
    }

    return $new_arr;

}

/**
 * Update key của array_1 bằng key của array_2.
 * Chỉ update những key có mặt ở cả array_1 và array_2.
 * Những key có ở array_1 nhưng không có ở array_2 sẽ được giữ nguyên giá trị.
 *
 * @param   array: array gốc
 * @paran   array: array chứa nội dung muốn thay đổi cho array_1
 *
 * @return  array: array_1 đã được update
 */
function array_update($array1, $array2) {
    foreach ($array1 as $key => $val) {
        if (isset($array2[$key])) {
            $array1[$key] = $array2[$key];
        }
    }
    return $array1;
}

/* End of file MY_date_helper.php */
/* Location: ./application/helpers/MY_date_helper.php */
