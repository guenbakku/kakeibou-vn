<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_model extends App_Model {
    
    const TABLE = 'inout_records';
    
    protected $settings = [
        'amount'            => null,
        'memo'              => null,
        'inout_from'        => null,
        'inout_to'          => null,
        'inout_type'        => null,
        'modified_from'     => null,
        'modified_to'       => null,
        'account'           => null,
        'player'            => null,
        'hide_pair_inout'   => false,
        'offset'            => 0,
        'limit'             => 100,
    ];
    
    public $result;
    public $next;
    public $total;
    
    public function __construct() {
        $this->load->model('inout_type_model');
    }
        
    public function __set(string $name, $val)
    {
        if ($val === null){
            return false;
        }
        
        if (!in_array($name, array_keys($this->settings), true)){
            throw new AppException($name . ' không tồn tại');
        }
        
        if ($name === 'amount'){
            if (!is_numeric($val) || $val < 0){
                throw new AppException('Dữ liệu số tiền không hợp lệ');
            }
        }
        else if ($name === 'player'){
            if (!is_numeric($val)){
                throw new AppException('Dữ liệu người phụ trách không hợp lệ');
            }
        }
        else if ($name === 'inout_type'){
            if (!is_numeric($val) || !in_array($val, [0, 1, 2])){
                throw new AppException('Dữ liệu loại thu chi không hợp lệ');
            }
        }
        else if ($name === 'account'){
            if (!is_numeric($val)){
                throw new AppException('Dữ liệu loại tài khoản không hợp lệ');
            }
        }
        else if (in_array($name, ['inout_from', 'inout_to', 'modified_from', 'modified_to'])){
            // Quăng ngoại lệ nếu val không có dạng yyyy-mm-dd
            // hoặc không phải là ngày tháng năm có nghĩa
            if (!preg_match('/^\d{4}(\-\d{2})?(\-\d{2})?$/', $val) || !strtotime($val) ) {
                throw new AppException('Dữ liệu ngày tháng ('.$name.') không hợp lệ');
            }
        }
        else if (in_array($name, ['offset', 'limit'])) {
            $val = is_bool($val)? $val : (int)$val;
            if ($val < 0) {
                throw new AppException('Dữ liệu '.$name.' không hợp lệ');
            }
        }
        else if ($name === 'hide_pair_inout')
        {
            $val = (bool) $val;
        }
        
        $this->settings[$name] = $val;
    }

    /**
     * Thực hiện tìm kiếm
     *
     * @param   void
     * @return  array
     */
    public function search(): array 
    {
        $db = $this->gen_search_query();
        $total_num = $this->total_num($db);
        $has_next_page = $this->has_next_page($db);
        
        $result = $db->limit($this->settings['limit'])
                     ->offset($this->settings['offset'])
                     ->get()->result_array();
        $fragment_num = $this->fragment_num($result, $has_next_page);
        
        $this->total = $total_num;
        $this->result = array_slice($result, 0, count($result) - $fragment_num);
        $this->next = $has_next_page
                      ? $this->settings['offset'] + $this->settings['limit'] - $fragment_num
                      : 0;

        return $this->result;
    }
    
    /**
     * Tạo url cho next page
     *
     * @param   void
     * @return  string
     */
    public function next_page_url(): ?string
    {
        if (!$this->next) {
            return null;
        }
        else {
            $query = $this->input->get();
            $query['offset'] = $this->next;
            return current_url().'?'.http_build_query($query);
        }
    }
    
    /**
     * Tạo query cho xử lý tìm kiếm. 
     * Dữ liệu sử dụng để tạo query lấy từ property settings
     *
     * @param   void
     * @return  object: db object
     */
    protected function gen_search_query()
    {        
        // Set dữ liệu cần lấy
        $this->db->select('inout_records.id,
                           inout_records.amount,
                           inout_records.memo,
                           inout_records.date,
                           inout_types.name AS inout_type,
                           accounts.name AS account,
                           accounts.id AS account_id,
                           accounts.icon AS account_icon,
                           categories.name AS category,
                           users.fullname AS player,
                           users.label AS player_label')
                 ->from(self::TABLE)
                 ->join('accounts', 'accounts.id = inout_records.account_id')
                 ->join('categories', 'categories.id = inout_records.category_id')
                 ->join('inout_types', 'inout_types.id = categories.inout_type_id')
                 ->join('users', 'users.id = inout_records.player')
                 ->order_by('inout_records.date', 'DESC')
                 ->order_by('categories.inout_type_id', 'ASC')
                 ->order_by('inout_records.created_on', 'ASC');
        
        // Set điều kiện tìm kiếm
        if ($this->settings['amount'] != null){ // Chú ý không phải là kiểm tra empty vì muốn xét luôn trường hợp nhập 0
            $this->db->where('ABS(`inout_records`.`amount`)', $this->settings['amount'], false);
        }
        if (!empty($this->settings['memo'])){
            $parts = explode(' ', trim($this->settings['memo']));
            foreach ($parts as $part){
                $this->db->like('inout_records.memo', $part);
            }
        }
        if (!empty($this->settings['inout_type'])){
            if ($this->settings['inout_type'] == array_flip($this->inout_type_model::$INOUT_TYPE)['Thu']){
                $this->db->where('inout_records.amount >=', 0);
            }
            else{
                $this->db->where('inout_records.amount <', 0);
            }
        }
        if ($this->settings['account']>0){
            $this->db->where('inout_records.account_id', $this->settings['account']);
        }
        if (!empty($this->settings['player'])){
            $this->db->where('inout_records.player', $this->settings['player']);
        }
        if (!empty($this->settings['inout_from'])){
            $this->db->where('inout_records.date >=', $this->settings['inout_from']);
        }
        if (!empty($this->settings['inout_to'])){
            $this->db->where('inout_records.date <=', $this->settings['inout_to']);
        }
        if (!empty($this->settings['modified_from'])){
            $this->db->where('inout_records.modified_on >=', date('Y-m-d H:i:s', strtotime($this->settings['modified_from'])));
        }
        if (!empty($this->settings['modified_to'])){
            $this->db->where('inout_records.modified_on <', date('Y-m-d H:i:s', strtotime($this->settings['modified_to'] . ' +1 days')));
        }
        if ($this->settings['hide_pair_inout'] === true){
            $this->db->where('inout_records.pair_id', '');
        }
        
        return $this->db;
    }
    
    /**
     * Kiểm tra xem có trang tiếp theo hay không
     *
     * @param   object: db object
     * @return  bool
     */
    protected function has_next_page($db_obj): bool
    {
        if ($this->settings['limit'] === false) {
            return false;
        } 
        else {
            $db = clone $db_obj;
            $has_next_page = $db->offset($this->settings['offset'] + $this->settings['limit'])
                                ->limit(1)
                                ->get()->num_rows();
            return $has_next_page > 0;
        }
    }
    
    /**
     * Đếm tổng số kết quả tìm được
     *
     * @param   object: db object
     * @return  int
     */
    protected function total_num($db_obj): int
    {
        $db = clone $db_obj;
        return $db->count_all_results();
    }
    
    /**
     * Tùy vào điều kiện tìm kiếm mà kết quả tìm kiếm của 1 trang có thể 
     * bị cắt ở giữa chừng ngày cuối cùng trong danh sách.
     * Ở đây sẽ đếm số item của ngày cuối cùng trong danh sách kết quả 
     * tìm kiếm. 
     * Số item này sẽ được cắt bỏ để đảm bảo list kết quả tìm kiếm 
     * chỉ chứa những ngày có đủ số kết quả.
     * 
     * @param   array: result
     * @param   bool: có trang tiếp theo hay không
     * @return  int
     */
    protected function fragment_num(array $result, bool $has_next_page): int
    {
        // Nếu trang sau không có kết quả thì không cần phải cắt phần lẻ
        if ($has_next_page == 0) {
            return 0;
        }

        // Đếm số phần tử lẻ loi
        $fragment_num = 0;
        for ($i=count($result)-1; $i > 0; $i--) {
            $fragment_num++;
            if ($result[$i]['date'] !== $result[$i-1]['date']) {
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