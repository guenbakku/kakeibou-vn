<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Search_model extends App_Model
{
    public $result;
    public $next;
    public $num_of_results;
    public $results_sum;

    protected $settings = [
        'amount' => null,
        'memo' => null,
        'inout_from' => null,
        'inout_to' => null,
        'inout_type' => null,
        'modified_from' => null,
        'modified_to' => null,
        'account' => null,
        'player' => null,
        'only_show_temp_inout' => false,
        'also_show_pair_inout' => false,
        'offset' => 0,
        'limit' => 100,
    ];

    public function __construct()
    {
        $this->load->model('inout_type_model');
    }

    public function __set(string $name, $val)
    {
        if (null === $val) {
            return false;
        }

        if (!in_array($name, array_keys($this->settings), true)) {
            throw new AppException($name.' không tồn tại');
        }

        if ('amount' === $name) {
            if (!is_numeric($val) || $val < 0) {
                throw new AppException('Dữ liệu số tiền không hợp lệ');
            }
        } elseif ('player' === $name) {
            if (!is_numeric($val)) {
                throw new AppException('Dữ liệu người phụ trách không hợp lệ');
            }
        } elseif ('inout_type' === $name) {
            if (!is_numeric($val) || !in_array($val, [0, 1, 2])) {
                throw new AppException('Dữ liệu loại thu chi không hợp lệ');
            }
        } elseif ('account' === $name) {
            if (!is_numeric($val)) {
                throw new AppException('Dữ liệu loại tài khoản không hợp lệ');
            }
        } elseif (in_array($name, ['inout_from', 'inout_to', 'modified_from', 'modified_to'])) {
            // Quăng ngoại lệ nếu val không có dạng yyyy-mm-dd
            // hoặc không phải là ngày tháng năm có nghĩa
            if (!preg_match('/^\d{4}(\-\d{2})?(\-\d{2})?$/', $val) || !strtotime($val)) {
                throw new AppException('Dữ liệu ngày tháng ('.$name.') không hợp lệ');
            }
        } elseif (in_array($name, ['offset', 'limit'])) {
            $val = is_bool($val) ? $val : (int) $val;
            if ($val < 0) {
                throw new AppException('Dữ liệu '.$name.' không hợp lệ');
            }
        } elseif ('also_show_pair_inout' === $name) {
            $val = (bool) $val;
        } elseif ('only_show_temp_inout' === $name) {
            $val = (bool) $val;
        }

        $this->settings[$name] = $val;
    }

    public function get_table(): string
    {
        return 'inout_records';
    }

    /**
     * Thực hiện tìm kiếm.
     */
    public function search(): array
    {
        $db = $this->gen_search_query();
        $num_of_results = $this->get_num_of_results($db);
        $has_next_page = $this->has_next_page($db);

        $result = $db->limit($this->settings['limit'])
            ->offset($this->settings['offset'])
            ->get()->result_array()
        ;
        $fragment_num = $this->count_fragment_num($result, $has_next_page);

        $this->num_of_results = $num_of_results;
        $this->result = array_slice($result, 0, count($result) - $fragment_num);
        $this->next = $has_next_page
                      ? $this->settings['offset'] + $this->settings['limit'] - $fragment_num
                      : 0;
        $this->results_sum = $this->sum_amount_of_all_results();

        return $this->result;
    }

    /**
     * Tạo url cho next page.
     */
    public function next_page_url(): ?string
    {
        if (!$this->next) {
            return null;
        }

        $query = $this->input->get();
        $query['offset'] = $this->next;

        return current_url().'?'.http_build_query($query);
    }

    protected function get_fresh_query()
    {
        $db = clone $this->db;
        $db->reset_query();

        return $db;
    }

    /**
     * Tạo query cho xử lý tìm kiếm.
     */
    protected function gen_search_query()
    {
        $db = $this->get_fresh_query();

        // Set dữ liệu cần lấy
        $db->select('inout_records.id,
                           inout_records.amount,
                           inout_records.memo,
                           inout_records.date,
                           inout_records.skip_month_estimated,
                           inout_records.is_temp,
                           inout_types.name AS inout_type,
                           inout_types.id AS inout_type_id,
                           accounts.name AS account,
                           accounts.id AS account_id,
                           accounts.icon AS account_icon,
                           categories.name AS category,
                           users.fullname AS player,
                           users.label AS player_label')
            ->order_by('inout_records.date', 'DESC')
            ->order_by('categories.inout_type_id', 'ASC')
            ->order_by('inout_records.created_on', 'ASC')
        ;

        return $this->add_where_query($db);
    }

    /**
     * Add điều kiện where và join table để tìm kiếm.
     * Dữ liệu sử dụng để tạo query lấy từ property settings.
     *
     * @param object $db
     *
     * @return object db object
     */
    protected function add_where_query($db)
    {
        $db->from($this->get_table())
            ->join('accounts', 'accounts.id = inout_records.account_id')
            ->join('categories', 'categories.id = inout_records.category_id')
            ->join('inout_types', 'inout_types.id = categories.inout_type_id')
            ->join('users', 'users.id = inout_records.player')
        ;

        // Set điều kiện tìm kiếm
        if (null != $this->settings['amount']) { // Chú ý không phải là kiểm tra empty vì muốn xét luôn trường hợp nhập 0
            $db->where('ABS(`inout_records`.`amount`)', $this->settings['amount'], false);
        }
        if (!empty($this->settings['memo'])) {
            $parts = explode(' ', trim($this->settings['memo']));
            foreach ($parts as $part) {
                $db->like('inout_records.memo', $part);
            }
        }
        if (!empty($this->settings['inout_type'])) {
            if ($this->settings['inout_type'] == array_flip($this->inout_type_model::$INOUT_TYPE)['Thu']) {
                $db->where('inout_records.amount >=', 0);
            } else {
                $db->where('inout_records.amount <', 0);
            }
        }
        if ($this->settings['account'] > 0) {
            $db->where('inout_records.account_id', $this->settings['account']);
        }
        if (!empty($this->settings['player'])) {
            $db->where('inout_records.player', $this->settings['player']);
        }
        if (!empty($this->settings['inout_from'])) {
            $db->where('inout_records.date >=', $this->settings['inout_from']);
        }
        if (!empty($this->settings['inout_to'])) {
            $db->where('inout_records.date <=', $this->settings['inout_to']);
        }
        if (!empty($this->settings['modified_from'])) {
            $db->where('inout_records.modified_on >=', date('Y-m-d H:i:s', strtotime($this->settings['modified_from'])));
        }
        if (!empty($this->settings['modified_to'])) {
            $db->where('inout_records.modified_on <', date('Y-m-d H:i:s', strtotime($this->settings['modified_to'].' +1 days')));
        }
        if (false === $this->settings['also_show_pair_inout']) {
            $db->where('inout_records.pair_id', '');
        }
        if (true === $this->settings['only_show_temp_inout']) {
            $db->where('inout_records.is_temp', 1);
        }

        return $db;
    }

    /**
     * Tính tổng thu chi của tất cả kết quả tìm kiếm.
     */
    protected function sum_amount_of_all_results()
    {
        $db = $this->get_fresh_query();
        $db->select('inout_records.amount as amount, categories.inout_type_id AS inout_type_id');
        $db = $this->add_where_query($db);
        $subQuery = $db->get_compiled_select();

        $db = $this->get_fresh_query();
        $sum_amount = $db->select('
                SUM(`amount`) AS `tong`,
                SUM(CASE WHEN `t`.`inout_type_id` = 1 THEN `amount` ELSE 0 END) AS `thu`,
                SUM(CASE WHEN `t`.`inout_type_id` = 2 THEN `amount` ELSE 0 END) AS `chi`')
            ->from("({$subQuery}) t")
            ->get()->result_array()
        ;

        return array_shift($sum_amount);
    }

    /**
     * Kiểm tra xem có trang tiếp theo hay không.
     *
     * @param object $db_obj db object
     */
    protected function has_next_page($db_obj): bool
    {
        if (false === $this->settings['limit']) {
            return false;
        }

        $db = clone $db_obj;
        $has_next_page = $db->offset($this->settings['offset'] + $this->settings['limit'])
            ->limit(1)
            ->get()->num_rows()
        ;

        return $has_next_page > 0;
    }

    /**
     * Đếm tổng số kết quả tìm được.
     *
     * @param object $db_obj db object
     */
    protected function get_num_of_results($db_obj): int
    {
        $db = clone $db_obj;

        return $db->count_all_results();
    }

    /**
     * Đếm số item của ngày cuối cùng trong danh sách kết quả tìm kiếm.
     * Tùy vào điều kiện tìm kiếm mà kết quả tìm kiếm của 1 trang có thể
     * bị cắt ở giữa chừng ngày cuối cùng trong danh sách.
     * Số item này sẽ được cắt bỏ để đảm bảo list kết quả tìm kiếm
     * chỉ chứa những ngày có đủ số kết quả.
     *
     * @param array $result        result
     * @param bool  $has_next_page có trang tiếp theo hay không
     */
    protected function count_fragment_num(array $result, bool $has_next_page): int
    {
        // Nếu không có trang tiếp theo không cần phải cắt phần lẻ
        if (true === $has_next_page) {
            return 0;
        }

        // Đếm số phần tử lẻ loi
        $fragment_num = 0;
        for ($i = count($result) - 1; $i > 0; --$i) {
            ++$fragment_num;
            if ($result[$i]['date'] !== $result[$i - 1]['date']) {
                break;
            }
        }

        // Nếu số lẻ loi bằng tổng số kết quả (tức danh sách kết quả chỉ chứa 1 ngày)
        // thì không cần phải cắt phần lẻ
        if ($fragment_num == count($result)) {
            return 0;
        }

        return $fragment_num;
    }
}
