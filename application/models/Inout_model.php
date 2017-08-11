<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inout_model extends App_Model {
    
    const TABLE = 'inout_records';
    
    // ID của Account Tiền mặt trong Table Categories (chú ý kiểu String)
    const ACCOUNT_CASH_ID = '1';
    
    // Mốc đánh dấu ID kết thúc của category fix
    const FIX_CATEGORY_ID_MAX = '20';
    
    // Ký tự nối account & player trong select cho nút transfer
    const TRANSFER_SELECT_GLUE = '-';
    
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
     * Nếu trong pair có 1 item là tài khoản ngân hàng thì mặc định đó là item đầu tiên
     */    
    public static $CASH_FLOW_NAMES = [
        'outgo'     => ['Thêm khoản chi', 2],
        'income'    => ['Thêm khoản thu', 1],
        'internal'  => ['Chuyển nội bộ*', 2],
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
    
    public function get(int $id)
    {
        return $this->db->where('inout_records.id', $id)
                        ->join('categories', 'categories.id = inout_records.category_id')
                        ->limit(1)
                        ->get(self::TABLE)
                        ->row_array();
    }
    
    public function add(string $type, array $data)
    {   
        $data['cash_flow']  = $type;
        $data['created_on'] = $data['modified_on'] = date('Y-m-d H:i:s');
        $data['created_by'] = $data['modified_by'] = $this->auth->user('id');
        
        $this->db->trans_start();
        foreach ($this->set_pair_add_data($type, $data) as $item){
            $item = $this->remove_garbage_fields($item);
            $this->db->insert(self::TABLE, $item);
        }
        $this->db->trans_complete();
    }
    
    public function edit($id, array $data)
    {
        $data['modified_on'] = date('Y-m-d H:i:s');
        $data['modified_by'] = $this->auth->user('id');
        
        $this->db->trans_start();
        foreach ($this->set_pair_edit_data($id, $data) as $item){
            $item = $this->remove_garbage_fields($item);
            $this->db->where('id', $item['id'])
                     ->update(self::TABLE, $item);
        }
        $this->db->trans_complete();
    }
    
    public function del(int $id)
    {
        $pair = $this->get_pair_data($id);
        
        $this->db->trans_start();
        foreach ($pair as $i => $item){
            $this->db->where('id', $item['id'])
                     ->delete(self::TABLE);
        }
        $this->db->trans_complete();
    }
    
    public function search_memo(string $q)
    {
        $q = $this->db->escape_like_str($q);
        $sql = "SELECT `memo` 
                FROM (SELECT `memo`, COUNT(`memo`) as `count`
                      FROM `inout_records`
                      WHERE `memo` LIKE '%{$q}%'
                      GROUP BY `memo`) AS t
                ORDER BY `count`
                LIMIT 0, 10";
        
        return array_column($this->db->query($sql)->result_array(), 'memo');
    }
    
    /**
     * Tạo pair dữ liệu cho thao tác add
     * 
     * @param   string: type inout
     * @param   array: dữ liệu form
     * @return  array
     */
    private function set_pair_add_data(string $type, array $data)
    {
        // Nếu không phải loại thao tác tạo ra dữ liệu lưu động nội bộ
        if (in_array($type, ['outgo', 'income'])){
            return [$data];
        }
        
        if (!isset($data['transfer_from']) || !isset($data['transfer_to'])) {
            throw new AppException(Consts::ERR_BAD_REQUEST);
        }
        if ($data['transfer_from'] == $data['transfer_to']) {
            throw new AppException(Consts::ERR_TRANSFER_FROM_TO_SAME);
        }
        
        $pair = [$data, $data];
        $pair[0]['pair_id'] = $pair[1]['pair_id'] = random_string('unique');
        $pair[0]['amount'] = 0-$pair[0]['amount'];
        
        list($pair[0]['account_id'], $pair[0]['player']) = $this->extract_transfer_code($data['transfer_from']);
        list($pair[1]['account_id'], $pair[1]['player']) = $this->extract_transfer_code($data['transfer_to']);
        
        $pair[0]['category_id'] = $this->get_internal_category_id($pair);
        $pair[1]['category_id'] = $pair[0]['category_id']+1;
        
        return $pair;
    }
    
    /**
     * Tạo pair dữ liệu cho thao tác edit
     * 
     * @param   int: id của record muốn sửa
     * @param   array: dữ liệu form
     * @return  array
     */
    private function set_pair_edit_data(int $id, array $data)
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
            $pair[0]['amount'] = $amount_sign * ABS($data['amount']);
            return $pair;
        }
        
        if ($data['transfer_from'] == $data['transfer_to']) {
            throw new AppException(Consts::ERR_TRANSFER_FROM_TO_SAME);
        }
        
        foreach ($pair as $i => $val) {
            $pair[$i] = array_merge($pair[$i], $data);
            $amount_sign = $this::$INOUT_TYPE_SIGN[$pair[$i]['inout_type_id']];
            $pair[$i]['amount'] = $amount_sign * ABS($data['amount']);
            $transfer = $i==0? $data['transfer_from'] : $data['transfer_to'];
            list($pair[$i]['account_id'], $pair[$i]['player']) = $this->extract_transfer_code($transfer);
        }
        
        $pair[0]['category_id'] = $this->get_internal_category_id($pair);
        $pair[1]['category_id'] = $pair[0]['category_id']+1;
        
        $pair = $this->modify_pair_player($pair);
        
        return $pair;
    }
    
    /**
     * Lấy pair dữ liệu của record có id được truyền
     *
     * @param   int: id
     * @return  array
     */
    private function get_pair_data(int $inout_id) {
        $select = [
            'inout_records.id',
            'inout_records.pair_id',
            'inout_records.account_id',
            'inout_records.player',
            'categories.inout_type_id',
        ];
        
        $res = $this->db->select($select)
                        ->from(self::TABLE)
                        ->join('categories', 'categories.id = inout_records.category_id')
                        ->where('inout_records.id', $inout_id)
                        ->limit(1)
                        ->get()->result_array();
        
        if (empty($res)){
            throw new AppException(Consts::ERR_BAD_REQUEST);
        }
        
        $pair_id = $res[0]['pair_id'];
        if (empty($pair_id)) {
            return $res;
        }
        
        $res = $this->db->select($select)
                        ->from(self::TABLE)
                        ->join('categories', 'categories.id = inout_records.category_id')
                        ->order_by('inout_records.id')
                        ->where('inout_records.pair_id', $pair_id)
                        ->limit(2)
                        ->get()->result_array();
                        
        return $res;
    }
    
    /**
     * Tách transfer_from hoặc transfer_to thành account và player
     *
     * @param   string: transfer code
     * @return  array: ['account_id' => ..., 'player' => ...]
     */
    private function extract_transfer_code($transfer)
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
     * nếu 1 item trong pair là cash và 1 item còn lại là tài khoản ngân hàng
     *
     * @param   array: pair data
     * @return  array: pair data đã sửa
     */
    private function modify_pair_player($pair) {
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

    /**
     * Trả về dữ liệu để tạo nút select cho ô transfer_from và transfer_to
     *
     * @param void
     * @return array
     */
    public function get_select_tag_data_for_transfer() {
        $this->load->model('user_model');
        $this->load->model('account_model');
        $current_player_id = $this->auth->user('id');
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
     * Tính toán giá trị transfer_from và transfer_to cho dữ liệu trong database
     * 
     * @param   array: dữ liệu inout trong db
     * @return  array: giá trị của transfer_from, transfer_to
     */
    public function get_transfer_code(array $data) {
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
                         ->from(self::TABLE)
                         ->get()->result_array();
        
        if (empty($pair)) {
            throw new AppException(ERR_NOT_FOUND);
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
        return isset(self::$CASH_FLOW_NAMES[$type])? self::$CASH_FLOW_NAMES[$type][0] : null;
    }
    
    public function get_inout_type_id(string $type)
    {      
        return isset(self::$CASH_FLOW_NAMES[$type])? self::$CASH_FLOW_NAMES[$type][1] : null;
    }
    
    public function get_inout_type_sign(string $type)
    {   
        if (!is_numeric($type) && is_string($type)){
            $type = $this->get_inout_type_id($type);
        }

        if (!is_numeric($type) || !isset(self::$INOUT_TYPE[$type])){
            return null;
        }

        return intval($type) === array_flip(self::$INOUT_TYPE)['Thu']
               ? '+' : '-';
    }
    
    /**
     * Lấy code của category nội bộ dành cho các loại thu chi phát sinh pair
     *
     * @param   string
     * @param   int
     */
    public function get_internal_category_id($pair)
    {
        if ($pair[0]['account_id'] == self::ACCOUNT_CASH_ID) {
            if ($pair[1]['account_id'] == self::ACCOUNT_CASH_ID) {
                return self::$INTERNAL_CATEGORY_IDS['handover'];
            }
            else {
                return self::$INTERNAL_CATEGORY_IDS['deposit'];
            }
        } else {
            if ($pair[1]['account_id'] == self::ACCOUNT_CASH_ID) {
                return self::$INTERNAL_CATEGORY_IDS['drawer'];
            }
            else {
                return self::$INTERNAL_CATEGORY_IDS['transfer'];
            }
        }
    }
}