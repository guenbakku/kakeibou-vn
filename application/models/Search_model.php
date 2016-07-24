<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_model extends App_Model {
    
    private $amount          = null;
    private $memo            = null;
    private $from            = null;
    private $to              = null;
    private $inout_type      = null;
    private $player          = null;
    private $show_pair_inout = false;
    
    private $result     = array();
    public function __set($name, $val)
    {
        if ($val === null){
            return false;
        }
        
        $acceptable_keys = array('amount', 'memo', 'from', 'to', 'inout_type', 'player', 'show_pair_inout');
        if (!in_array($name, $acceptable_keys, true)){
            throw new InvalidArgumentException($name . ' không tồn tại hoặc không được phép thay đổi');
        }
        
        if ($name === 'amount'){
            if (!is_numeric($val) || $val < 0){
                throw new InvalidArgumentException('Dữ liệu số tiền không hợp lệ.');
            }
        }
        else if ($name === 'player'){
            if (!is_numeric($val)){
                throw new InvalidArgumentException('Dữ liệu người phụ trách không hợp lệ.');
            }
        }
        else if ($name === 'inout_type'){
            if (!is_numeric($val) || !in_array($val, array(0, 1, 2))){
                throw new InvalidArgumentException('Dữ liệu loại thu chi không hợp lệ.');
            }
        }
        else if ($name === 'from' || $name === 'to'){
            // Quăng ngoại lệ nếu val không có dạng yyyy-mm-dd
            // hoặc không phải là ngày tháng năm có nghĩa
            if (!preg_match('/^\d{4}(\-\d{2})?(\-\d{2})?$/', $val)
                || !strtotime($val) )
            {
                throw new InvalidArgumentException('Dữ liệu ngày tháng ('.$name.') không hợp lệ.');
            }
        }
        else if ($name === 'show_pair_inout')
        {
            $val = (bool) $val;
        }
        
        $this->$name = $val;
    }
    
    public function search()
    {        
        // Set dữ liệu cần lấy
        $this->db->select('inout_records.iorid,
                           inout_records.amount,
                           inout_records.memo,
                           inout_records.date,
                           inout_types.name AS inout_type,
                           accounts.name AS account, 
                           categories.name AS category,
                           users.fullname AS player,
                           users.label AS player_label'
                         )
                 ->from('inout_records')
                 ->join('accounts', 'accounts.aid = inout_records.account_id')
                 ->join('categories', 'categories.cid = inout_records.category_id')
                 ->join('inout_types', 'inout_types.iotid = categories.inout_type_id')
                 ->join('users', 'users.uid = inout_records.player')
                 ->order_by('inout_records.date', 'ASC')
                 ->order_by('categories.inout_type_id', 'ASC')
                 ->order_by('inout_records.created_on', 'ASC');
        
        // Set điều kiện tìm kiếm
        if ($this->amount !== null){ // Chú ý so sánh là so sánh chính xác !== (xét luôn trường hợp nhập số 0)  
            $this->db->where('ABS(`inout_records`.`amount`)', $this->amount, false);
        }
        if ($this->inout_type != null){
            if ($this->inout_type == 1){
                $this->db->where('inout_records.amount >=', 0);
            }
            else{
                $this->db->where('inout_records.amount <', 0);
            }
        }
        if ($this->memo != null){
            $parts = explode(' ', trim($this->memo));
            foreach ($parts as $part){
                $this->db->like('inout_records.memo', $part);
            }
        }
        if ($this->player != null){
            $this->db->where('inout_records.player', $this->player);
        }
        if ($this->from != null){
            $this->db->where('inout_records.date >=', $this->from);
        }
        if ($this->to != null){
            $this->db->where('inout_records.date <=', $this->to);
        }
        if ($this->show_pair_inout === false){
            $this->db->where('inout_records.pair_id', '');
        }
        
        // return $this->db->get_compiled_select();
        return $this->result = $this->db->get()->result_array();
    }
}