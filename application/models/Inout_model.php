<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Inout_model extends App_Model
{
    // ID của Account Tiền mặt trong Table Categories (chú ý kiểu String)
    public const ACCOUNT_CASH_ID = '1';

    // Mốc đánh dấu ID kết thúc của category fix
    public const FIX_CATEGORY_ID_MAX = '20';

    // Ký tự nối account & player trong select cho nút transfer
    public const TRANSFER_SELECT_GLUE = '-';

    // Tên của loại thu chi
    public static $INOUT_TYPE = [
        1 => 'Thu',
        2 => 'Chi',
    ];

    // Dấu của loại thu chi
    public static $INOUT_TYPE_SIGN = [
        1 => 1,
        2 => -1,
    ];

    /**
     * Tên và phân loại thu chi cho từng loại dòng tiền
     * Thứ tự từng Item trong array:
     *      Tên đầy đủ
     *      Phân loại khoản thu chi (nếu là lưu động nội bộ thì là của item đầu tiên)
     * Nếu trong pair có 1 item là tài khoản ngân hàng thì mặc định đó là item đầu tiên.
     */
    public static $CASH_FLOW_NAMES = [
        'outgo' => ['Thêm khoản chi', 2],
        'income' => ['Thêm khoản thu', 1],
        'internal' => ['Chuyển nội bộ*', 2],
    ];

    public static $INTERNAL_CATEGORY_IDS = [
        'drawer' => 1,
        'deposit' => 3,
        'handover' => 5,
        'transfer' => 7,
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database('default');
    }

    public function get_table(): string
    {
        return 'inout_records';
    }

    public function get(int $id)
    {
        return $this->db->where('inout_records.id', $id)
            ->join('categories', 'categories.id = inout_records.category_id')
            ->limit(1)
            ->get($this->get_table())
            ->row_array()
        ;
    }

    /**
     * Add new inout record to database.
     *
     * @return int[] the list ids of inserted records
     */
    public function add(string $type, array $data): array
    {
        $data['cash_flow'] = $type;
        $data['created_on'] = $data['modified_on'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['modified_by'] = $this->auth->user('id');
        $insert_id = [];

        $this->db->trans_start();
        foreach ($this->set_pair_add_data($type, $data) as $item) {
            $item = $this->remove_garbage_fields($item);
            $this->db->insert($this->get_table(), $item);
            $insert_id[] = $this->db->insert_id();
        }
        $this->db->trans_complete();

        return $insert_id;
    }

    public function edit($id, array $data)
    {
        $data['modified_on'] = date('Y-m-d H:i:s');
        $data['modified_by'] = $this->auth->user('id');

        $this->db->trans_start();
        foreach ($this->set_pair_edit_data($id, $data) as $item) {
            $item = $this->remove_garbage_fields($item);
            $this->db->where('id', $item['id'])
                ->update($this->get_table(), $item)
            ;
        }
        $this->db->trans_complete();
    }

    public function del(int $id)
    {
        $pair = $this->get_pair_data($id);

        $this->db->trans_start();
        foreach ($pair as $i => $item) {
            $this->db->where('id', $item['id'])
                ->delete($this->get_table())
            ;
        }
        $this->db->trans_complete();
    }

    public function search_memo(string $keyword, string $cash_flow)
    {
        $keyword = trim($keyword);
        if ($keyword === '') {
            return [];
        }

        // Construct sub query's sql
        $this->db->select('memo')
            ->select('COUNT(`memo`) as `count`')
            ->select('MAX(`modified_on`) as `modified_on`')
            ->select('cash_flow')
            ->from($this->get_table())
            ->where('cash_flow', $cash_flow)
            ->group_by('memo')
        ;

        // Search by keyword's parts
        array_map(
            function ($part) {
                $part = trim($part);
                $part = $this->db->escape_like_str($part);
                if ($part !== '') {
                    $this->db->like('memo', $part);
                }
            },
            explode(' ', $keyword)
        );

        $sub_sql = $this->db->get_compiled_select();

        $this->db->reset_query();
        $this->db->select('inout_records.id')
            ->select('inout_records.category_id')
            ->select('inout_records.account_id')
            ->select('inout_records.cash_flow')
            ->select('inout_records.memo as `value`')
            ->select('inout_records.is_temp')
            ->from("({$sub_sql}) AS t")
            ->join(
                'inout_records',
                'inout_records.memo = t.memo AND inout_records.modified_on = t.modified_on AND inout_records.cash_flow = t.cash_flow',
                'left'
            )
            ->where('inout_records.cash_flow !=', 'internal')
            ->or_where('inout_records.amount >=', 0)
            ->order_by('inout_records.modified_on', 'DESC')
            ->order_by('t.count', 'DESC')
            ->limit(10)
            ->offset(0)
        ;

        return $this->db->get()->result_array();
    }

    /**
     * Tạo pair_id.
     */
    public function gen_pair_id(): string
    {
        do {
            $pair_id = random_string('md5');
            $existed = $this->db->select('pair_id')
                ->from($this->get_table())
                ->where('pair_id', $pair_id)
                ->limit(1)
                ->get()->num_rows() > 0
            ;
        } while ($existed);

        return $pair_id;
    }

    /**
     * Trả về dữ liệu để tạo nút select cho ô transfer_from và transfer_to.
     */
    public function get_select_tag_data_for_transfer(): array
    {
        $this->load->model('user_model');
        $this->load->model('account_model');
        $player_select_tags = $this->user_model->get_select_tag_data();
        $account_select_tags = $this->account_model->get_select_tag_data();
        $glue = self::TRANSFER_SELECT_GLUE;
        $select_tags = [];
        foreach ($player_select_tags as $player_id => $name) {
            $key = implode($glue, [self::ACCOUNT_CASH_ID, $player_id]);
            $select_tags[$key] = $name;
        }
        unset($account_select_tags[self::ACCOUNT_CASH_ID]);
        $select_tags += $account_select_tags;

        return $select_tags;
    }

    /**
     * Tính toán giá trị transfer_from và transfer_to cho dữ liệu trong database.
     *
     * @param array $data dữ liệu inout trong db
     *
     * @return array giá trị của transfer_from, transfer_to
     */
    public function get_transfer_code(array $data)
    {
        $transfer = [
            'from' => null,
            'to' => null,
        ];

        if (empty($data['pair_id'])) {
            return $transfer;
        }

        $pair = $this->db->select('account_id')
            ->select('player')
            ->where('pair_id', $data['pair_id'])
            ->order_by('id', 'asc')
            ->from($this->get_table())
            ->get()->result_array()
        ;

        if (empty($pair)) {
            throw new AppException(settings('err_not_found'));
        }

        // Bỏ item player nếu ko phải là item cash
        $modifier = function ($item) {
            if ($item['account_id'] != self::ACCOUNT_CASH_ID) {
                unset($item['player']);
            }

            return $item;
        };

        $glue = self::TRANSFER_SELECT_GLUE;
        $transfer['from'] = implode($glue, $modifier($pair[0]));
        $transfer['to'] = implode($glue, $modifier($pair[1]));

        return $transfer;
    }

    public function get_cash_flow_name(string $type)
    {
        return isset(self::$CASH_FLOW_NAMES[$type]) ? self::$CASH_FLOW_NAMES[$type][0] : null;
    }

    public function get_inout_type_id(string $type)
    {
        return isset(self::$CASH_FLOW_NAMES[$type]) ? self::$CASH_FLOW_NAMES[$type][1] : null;
    }

    public function get_inout_type_sign(string $type)
    {
        if (!is_numeric($type) && is_string($type)) {
            $type = $this->get_inout_type_id($type);
        }

        if (!is_numeric($type) || !isset(self::$INOUT_TYPE[$type])) {
            return null;
        }

        return intval($type) === array_flip(self::$INOUT_TYPE)['Thu']
               ? '+' : '-';
    }

    /**
     * Lấy code của category nội bộ dành cho các loại thu chi phát sinh pair.
     */
    public function get_internal_category_id(array $pair)
    {
        if ($pair[0]['account_id'] == self::ACCOUNT_CASH_ID) {
            if ($pair[1]['account_id'] == self::ACCOUNT_CASH_ID) {
                return self::$INTERNAL_CATEGORY_IDS['handover'];
            }

            return self::$INTERNAL_CATEGORY_IDS['deposit'];
        }
        if ($pair[1]['account_id'] == self::ACCOUNT_CASH_ID) {
            return self::$INTERNAL_CATEGORY_IDS['drawer'];
        }

        return self::$INTERNAL_CATEGORY_IDS['transfer'];
    }

    /**
     * Tạo pair dữ liệu cho thao tác add.
     *
     * @param string $type type inout
     * @param array  $data dữ liệu form
     */
    private function set_pair_add_data(string $type, array $data): array
    {
        // Thêm dấu + - vào số tiền tùy vào loại dòng tiền
        $data['amount'] = intval($this->get_inout_type_sign($type).$data['amount']);

        // Nếu không phải loại thao tác tạo ra dữ liệu lưu động nội bộ
        if (in_array($type, ['outgo', 'income'])) {
            return [$data];
        }

        if (!isset($data['transfer_from']) || !isset($data['transfer_to'])) {
            throw new AppException(settings('err_bad_request'));
        }
        if ($data['transfer_from'] == $data['transfer_to']) {
            throw new AppException(settings('err_transfer_from_to_same'));
        }

        $pair = [$data, $data];
        $pair[0]['pair_id'] = $pair[1]['pair_id'] = $this->gen_pair_id();
        $pair[1]['amount'] = 0 - $pair[0]['amount'];

        list($pair[0]['account_id'], $pair[0]['player']) = $this->extract_transfer_code($data['transfer_from']);
        list($pair[1]['account_id'], $pair[1]['player']) = $this->extract_transfer_code($data['transfer_to']);

        $pair[0]['category_id'] = $this->get_internal_category_id($pair);
        $pair[1]['category_id'] = $pair[0]['category_id'] + 1;

        return $this->modify_pair_player($pair);
    }

    /**
     * Tạo pair dữ liệu cho thao tác edit.
     *
     * @param int   $id   id của record muốn sửa
     * @param array $data dữ liệu form
     */
    private function set_pair_edit_data(int $id, array $data): array
    {
        $pair = $this->get_pair_data($id);

        // Remove fields which can not be editable from $data
        unset(
            $data['pair_id'],
            $data['id'],
            $data['created_on'],
            $data['created_by']
        );

        // Nếu không phải loại thao tác tạo ra dữ liệu lưu động nội bộ
        if (count($pair) == 1) {
            $pair[0] = array_merge($pair[0], $data);
            $amount_sign = $this::$INOUT_TYPE_SIGN[$pair[0]['inout_type_id']];
            $pair[0]['amount'] = $amount_sign * abs($data['amount']);

            return $pair;
        }

        if ($data['transfer_from'] == $data['transfer_to']) {
            throw new AppException(settings('err_transfer_from_to_same'));
        }

        foreach ($pair as $i => $val) {
            $pair[$i] = array_merge($pair[$i], $data);
            $amount_sign = $this::$INOUT_TYPE_SIGN[$pair[$i]['inout_type_id']];
            $pair[$i]['amount'] = $amount_sign * abs($data['amount']);
            $transfer = $i == 0 ? $data['transfer_from'] : $data['transfer_to'];
            list($pair[$i]['account_id'], $pair[$i]['player']) = $this->extract_transfer_code($transfer);
        }

        $pair[0]['category_id'] = $this->get_internal_category_id($pair);
        $pair[1]['category_id'] = $pair[0]['category_id'] + 1;

        return $this->modify_pair_player($pair);
    }

    /**
     * Lấy pair dữ liệu của record có id được truyền.
     */
    private function get_pair_data(int $inout_id): array
    {
        $select = [
            'inout_records.id',
            'inout_records.pair_id',
            'inout_records.account_id',
            'inout_records.player',
            'categories.inout_type_id',
        ];

        $res = $this->db->select($select)
            ->from($this->get_table())
            ->join('categories', 'categories.id = inout_records.category_id')
            ->where('inout_records.id', $inout_id)
            ->limit(1)
            ->get()->result_array()
        ;

        if (empty($res)) {
            throw new AppException(settings('err_bad_request'));
        }

        $pair_id = $res[0]['pair_id'];
        if (empty($pair_id)) {
            return $res;
        }

        return $this->db->select($select)
            ->from($this->get_table())
            ->join('categories', 'categories.id = inout_records.category_id')
            ->order_by('inout_records.id')
            ->where('inout_records.pair_id', $pair_id)
            ->limit(2)
            ->get()->result_array()
        ;
    }

    /**
     * Tách transfer_from hoặc transfer_to thành account và player.
     *
     * @param string $transfer transfer code
     *
     * @return array ['account_id' => ..., 'player' => ...]
     */
    private function extract_transfer_code(string $transfer): array
    {
        $item = [
            0 => null,
            1 => $this->auth->user('id'),
        ];

        $transfer = explode(self::TRANSFER_SELECT_GLUE, $transfer);
        $transfer = array_slice($transfer, 0, 2);

        foreach ($transfer as $i => $val) {
            $item[$i] = $val;
        }

        return $item;
    }

    /**
     * Sửa player của pair data về player của item cash
     * nếu 1 item trong pair là cash và 1 item còn lại là tài khoản ngân hàng.
     *
     * @param array $pair pair data
     *
     * @return array pair data đã sửa
     */
    private function modify_pair_player($pair): array
    {
        if (!in_array(self::ACCOUNT_CASH_ID, [
            $pair[0]['account_id'],
            $pair[1]['account_id'],
        ])) {
            return $pair;
        }

        if ($pair[0]['account_id'] == $pair[1]['account_id']) {
            return $pair;
        }

        if ($pair[0]['account_id'] == self::ACCOUNT_CASH_ID) {
            $pair[1]['player'] = $pair[0]['player'];
        } else {
            $pair[0]['player'] = $pair[1]['player'];
        }

        return $pair;
    }
}
