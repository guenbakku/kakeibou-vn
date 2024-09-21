<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('day_of_week')) {
    /**
     * Tính thứ của một ngày cụ thể.
     *
     * @param int|string $date timestamp hoặc string theo format datetime
     *
     * @return string thứ trong tuần
     */
    function day_of_week(int|string $date): ?string
    {
        $days = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];

        if (!is_numeric($date)) {
            $date = strtotime($date);
        } else {
            $date = (int) $date;
        }

        if ($date === false) {
            return null;
        }

        return $days[date('w', $date)];
    }
}

if (!function_exists('extract_date_string')) {
    /**
     * Tách chuỗi date thành array('y' => yyyy, 'm' => mm, 'd' => đd).
     *
     * @param string $date            bắt buộc phải có ký tự ngăn cách giữa yyyy-mm-dd
     * @param bool   $includeNullItem có lấy cả những item null không
     */
    function extract_date_string(string $date, bool $includeNullItem = true): array
    {
        $date = preg_replace('/[^\d]+/', '-', $date);
        @list($year, $month, $day) = explode('-', $date);

        $year = $year ?? '';
        $month = $month ?? '';
        $day = $day ?? '';

        $extracted = ['y' => null, 'm' => null, 'd' => null];
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
            : array_filter($extracted, function ($item) {
                return $item !== null;
            });
    }
}

if (!function_exists('combine_date_string')) {
    /**
     * Tạo chuỗi date từ array
     * Có thể xem đây là hàm ngược của hàm extract_date_string().
     *
     * @param array  $date array chứa year, month, day
     * @param string $glue chuỗi để nối
     *
     * @return string chuỗi date đã nối
     */
    function combine_date_string(array $date, string $glue = '-'): string
    {
        $date = array_slice($date, 0, 3);

        return implode($glue, array_filter($date, function ($item) {
            return $item !== null;
        }));
    }
}

if (!function_exists('date_format_type_of_string')) {
    /**
     * Trả về loại format của một chuỗi ký tự kiểu date.
     * Hàm này sẽ cố gắng dùng regex để chuyển chuỗi về đúng format chuẩn trước khi kiểm tra.
     * Ví dụ:
     *  - 2016-12-31 -> 'ymd'
     *  - 2016/02 -> 'ym'
     *  - 2016 -> 'y'
     *  - other -> null.
     *
     * @param string $date chuỗi muốn kiểm tra
     */
    function date_format_type_of_string(string $date): ?string
    {
        $extracted_arr = extract_date_string($date, false);

        switch (count($extracted_arr)) {
            case 3:
                return 'ymd';

            case 2:
                return 'ym';

            case 1:
                return 'y';

            default:
                return null;
        }
    }
}

if (!function_exists('boundary_date')) {
    /**
     * Tính ngày giới hạn (bắt đầu và kết thúc) của một khoảng thời gian.
     * Ví dụ
     *  1. 2016         -> array('2016-01-01', '2016-12-31')
     *  2. 2016-12      -> array('2016-12-01', '2016-12-31')
     *  3. 2016-12-31   -> array('2016-12-31', '2016-12-31').
     *
     * @param int $year  year
     * @param int $month month
     * @param int $day   day
     *
     * @return array ngày đầu và cuối của khoảng thời gian đó
     */
    function boundary_date(string $year, ?int $month = null, ?int $day = null): array
    {
        // Tách parameter đầu tiên thành year, month, day
        // nếu parameter đầu tiên là chuỗi format kiểu date
        if (!is_numeric($year) && is_string($year)) {
            $extracted = extract_date_string($year);
            list('y' => $year, 'm' => $month, 'd' => $day) = $extracted;
        }

        if (!is_numeric($year)) {
            return [];
        }

        $range = [
            date('Y-m-d', strtotime($year.'-01-01')),
            date('Y-m-d', strtotime($year.'-12-31')),
        ];
        if (!is_numeric($month)) {
            return $range;
        }

        $range = [
            date('Y-m-d', strtotime($year.'-'.$month.'-01')),
            date('Y-m-t', strtotime($year.'-'.$month.'-01')),
        ];
        if (!is_numeric($day)) {
            return $range;
        }

        return [
            date('Y-m-d', strtotime($year.'-'.$month.'-'.$day)),
            date('Y-m-d', strtotime($year.'-'.$month.'-'.$day)),
        ];
    }
}

if (!function_exists('prev_next_time')) {
    /**
     * Tính ngày, tháng hoặc năm trước và sau của dữ liệu nhập vào.
     * Nếu dữ liệu nhập vào là dạng:
     *  * ngày  -> ngày trước và sau
     *  * tháng -> tháng trước và sau
     *  * năm   -> năm trước và sau.
     */
    function prev_next_time(string $date): array
    {
        $extracted = extract_date_string($date, false);

        switch (count($extracted)) {
            case 1:
                return [
                    $extracted['y'] - 1,
                    $extracted['y'] + 1,
                ];

            case 2:
                return [
                    date('Y-m', strtotime(implode('-', $extracted).'-01 -1 month')),
                    date('Y-m', strtotime(implode('-', $extracted).'-01 +1 month')),
                ];

            case 3:
                return [
                    date('Y-m-d', strtotime(implode('-', $extracted).' -1 day')),
                    date('Y-m-d', strtotime(implode('-', $extracted).' +1 day')),
                ];

            default:
                return [null, null];
        }
    }
}

if (!function_exists('months_list')) {
    /**
     * Tạo danh sách 12 tháng.
     */
    function months_list(): array
    {
        return array_map(
            function ($item) {
                return sprintf('%02d', $item);
            },
            range(1, 12)
        );
    }
}

if (!function_exists('days_list')) {
    /**
     * Tạo danh sách 31 ngày.
     */
    function days_list(): array
    {
        return array_map(
            function ($item) {
                return sprintf('%02d', $item);
            },
            range(1, 31)
        );
    }
}
