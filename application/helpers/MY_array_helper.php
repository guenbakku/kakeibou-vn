<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('array_gen_key')) {
    /**
     * Trả về array mới có index là giá trị của một column cụ thể có trong array.
     *
     * @param null|array  $arr array có ít nhất 2 chiều
     * @param null|string $key tên một trong những column trong array. Index của array mới sẽ được tạo từ các giá trị trong column này.
     * @param null|string $val tên một trong những column trong array.
     *                         - Nếu biến này được quy định, array sẽ được tạo với dạng array(key => value)
     *                         với value là giá trị trong cột được quy định ở biến này
     *                         - Nếu biến này là null, array sẽ được tạo với dạng array(key => item)
     *                         với item là item nguyên bản của array truyền vào
     *
     * @return array array mới đã được đánh index
     */
    function array_gen_key($arr = null, $key = null, $val = null)
    {
        if (!is_array($arr)) {
            return false;
        }

        // Kiểm tra sự tồn tại của colum trong array
        $first_item = reset($arr);
        if (!isset($first_item[$key]) || !is_string($first_item[$key])) {
            return $arr;
        }
        if ($val !== null && !isset($first_item[$val])) {
            return $arr;
        }

        $new_arr = [];
        if ($val === null) {
            foreach ($arr as $i => $item) {
                $new_arr[$item[$key]] = $item;
            }
        } else {
            foreach ($arr as $i => $item) {
                $new_arr[$item[$key]] = $item[$val];
            }
        }

        return $new_arr;
    }
}

if (!function_exists('array_update')) {
    /**
     * Update key của array_1 bằng key của array_2.
     * Chỉ update những key có mặt ở cả array_1 và array_2.
     * Những key có ở array_1 nhưng không có ở array_2 sẽ được giữ nguyên giá trị.
     *
     * @param array $array1 array gốc
     * @param array $array2 array chứa nội dung muốn thay đổi cho array_1
     *
     * @return array array_1 đã được update
     */
    function array_update(array $array1, array $array2): array
    {
        foreach ($array1 as $key => $val) {
            if (isset($array2[$key])) {
                $array1[$key] = $array2[$key];
            }
        }

        return $array1;
    }
}

if (!function_exists('array_to_list')) {
    /**
     * Chuyển 1 associate array (key-value) thành 1 number-index array (list).
     * Các phần tử của list này là 1 array con, mỗi array con chứa 2 phần tử có giá trị của `key` và `value`.
     */
    function array_to_list(array $arr)
    {
        return array_map(
            function ($v, $k) {
                return [$k, $v];
            },
            $arr,
            array_keys($arr),
        );
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('array_accessible')) {
    /**
     * Determine whether the given value is array accessible.
     *
     * @param mixed $value
     *
     * @return bool
     */
    function array_accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }
}

if (!function_exists('array_exists')) {
    /**
     * Determine if the given key exists in the provided array.
     *
     * @param array|ArrayAccess $array
     * @param int|string        $key
     *
     * @return bool
     */
    function array_exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }
}

if (!function_exists('array_get')) {
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array|ArrayAccess $array
     * @param string            $key
     * @param mixed             $default
     *
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (!array_accessible($array)) {
            return value($default);
        }
        if (is_null($key)) {
            return $array;
        }
        if (array_exists($array, $key)) {
            return $array[$key];
        }
        if (strpos($key, '.') === false) {
            return $array[$key] ?? value($default);
        }
        foreach (explode('.', $key) as $segment) {
            if (array_accessible($array) && array_exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }
}

// End of file MY_date_helper.php
// Location: ./application/helpers/MY_date_helper.php
