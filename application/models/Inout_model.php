<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inout_model extends App_Model {
    
    const TABLE = 'inout_records';
    
    // ID của Account Tiền mặt trong Table Categories (chú ý kiểu String)
    const ACCOUNT_CASH_ID = '1'; 
    
    // Mốc đánh dấu ID bắt đầu của category fix
    const FIX_CATEGORY_ID_MAX = '20';
    
    // Tên của loại thu chi
    public static $INOUT_TYPE = array(
        1 => 'Thu',
        2 => 'Chi',
    );
    
    // Dấu của loại thu chi
    public static $INOUT_TYPE_SIGN = array(
        1 => 1,
        2 => -1,
    );
    
    // Tên và phân loại thu chi cho từng loại dòng tiền
    // Thứ tự từng Item trong array:
    //      Tên đầy đủ
    //      Phân loại khoản thu chi (nếu là 1 pair thu chi thì là của item đầu tiên)
    //      ID của Category đại diện (nếu là 1 pair thu chi thì là của item đầu tiên, nếu 0: không có Category đại diện)
    // Nếu trong pair có 1 item là tài khoản ngân hàng thì mặc định đó là item đầu tiên
    public static $CASH_FLOW_NAMES = array(
        'outgo'     => array('Thêm khoản chi', 2, 0),
        'income'    => array('Thêm khoản thu', 1, 0),
        'drawer'    => array('Rút tiền từ tài khoản*', 2, 1),
        'deposit'   => array('Nạp tiền vô tài khoản*', 1, 3),
        'handover'  => array('Chuyển tiền qua tay*', 2, 5),
    );
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database('default');
    }
    
    public function get($id)
    {
        return $this->db->where('iorid', $id)
                        ->join('categories', 'categories.cid = inout_records.category_id')
                        ->limit(1)
                        ->get(self::TABLE)
                        ->row_array();
    }
    
    public function add($type, $data)
    {
        $this->db->trans_start();
        foreach ($this->setPairAddData($type, $data) as $item){
            $this->db->insert(self::TABLE, $item);
        }
        $this->db->trans_complete();
    }
    
    public function edit($id, $data)
    {
        $pair_data = $this->getPairId($id);
        
        // if ($pair_data === false){
            // throw new Exception($Constants::ERR_BAD_REQUEST);
        // }
        
        $this->db->trans_start();
        foreach ($pair_data as $iorid => $inout_type_id){
            $data['amount'] = $this::$INOUT_TYPE_SIGN[$inout_type_id] * ABS($data['amount']);
            $this->db->where('iorid', $iorid)->update(self::TABLE, $data);
        }
        $this->db->trans_complete();
    }
    
    public function del($id)
    {
        $this->db->trans_start();
        foreach ($this->getPairId($id) as $item){
            $this->db->where('iorid', $item)->delete(self::TABLE);
        }
        $this->db->trans_complete();
    }
    
    public function searchMemo($q)
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
    
    private function setPairAddData($type, $data)
    {
        $data['cash_flow']  = $type;
        $data['created_on'] = date('Y-m-d H:i:s');
        $data['created_by'] = $this->login_model->getInfo('uid');
        
        $pair[0] = $data;
        $pair[0]['amount']  = $this->getInoutTypeCode($type)==1? $pair[0]['amount'] : 0-$pair[0]['amount'];
        
        // Không phải loại theo tác tạo ra pair dữ liệu
        if (in_array($type, array('outgo', 'income'))){
            return $pair;
        }
        
        // Tạo pair dữ liệu
        $pair[1] = $pair[0];
        $pair[0]['pair_id'] = $pair[1]['pair_id'] = random_string('unique');
        $pair[1]['amount']  = 0 - $pair[0]['amount'];
        $pair[0]['category_id'] = $this->getFixCategoryCode($type);
        $pair[1]['category_id'] = $pair[0]['category_id']+1;
        
        if ($type == 'drawer' || $type == 'deposit'){
            $pair[1]['account_id']  = self::ACCOUNT_CASH_ID;
        }
        elseif ($type == 'handover'){
            $pair[0]['account_id'] = $pair[1]['account_id'] = self::ACCOUNT_CASH_ID;
            $pair[0]['player'] = $data['player'][0];
            $pair[1]['player'] = $data['player'][1];
        }
        
        return $pair;
    }
    
    /*
     *--------------------------------------------------------------------
     * Xét xem dữ liệu đang sửa có phải là dữ liệu lưu động nội bộ (có pair_id) hay không
     * Nếu không phải dữ liệu lưu động nội bộ thì trả về array chứa 1 item
     * Nếu là dữ liệu lưu động nội bộ thì trả về array chứa 2 item, 
     *  1 là của dữ liệu đang sửa, 1 là của item còn lại trong cặp
     * Array trả về sẽ có dạng (id => inout_type_id)
     * 
     *--------------------------------------------------------------------
     */
    private function getPairId($id)
    {
        $res = $this->db->select('inout_records.iorid')
                        ->select('inout_records.pair_id')
                        ->select('categories.inout_type_id')
                        ->from('inout_records')
                        ->join('categories', 'categories.cid = inout_records.category_id')
                        ->where('iorid', $id)
                        ->limit(1)
                        ->get()->result_array();
        
        // Id không có trong CSDL
        if (empty($res)){
            return false;
        }
        
        // Nếu là dữ liệu lưu động nội bộ thì lấy dữ liệu của item còn lại
        $pair_id = $res[0]['pair_id'];
        if (!empty($pair_id)){
            $res = $this->db->select('inout_records.iorid')
                            ->select('categories.inout_type_id')
                            ->from('inout_records')
                            ->join('categories', 'categories.cid = inout_records.category_id')
                            ->where('pair_id', $pair_id)
                            ->limit(2)
                            ->get()->result_array();
        }

        return array_column($res, 'inout_type_id', 'iorid');
    }
    
    /*
     *--------------------------------------------------------------------
     * Tính toán ID của người chuyển và người nhận khi cash_flow=handover
     * Cách tính:
     *      Nếu inout_type_id của data đang sửa = 2 (Chi) 
     *              -> Người chuyển là player của data đang xét
     *      Nếu inout_type_id của data đang sửa = 1 (Thu)
     *              -> Người chuyển là người còn lại
     *--------------------------------------------------------------------
     */
    public function setPlayersForHandoverEdit($data)
    {
        if ($data['cash_flow'] != 'handover'){
            return false;
        }
        
        $players = array($data['player'], 3-$data['player']);
        
        if ($data['inout_type_id'] == array_flip(self::$INOUT_TYPE)['Chi']){
            return $players;
        }
        else {
            return array_reverse($players);
        }
    }
    
    public function getCashFlowName($type)
    {
        if (!isset(self::$CASH_FLOW_NAMES[$type])){
            return false;
        }
        
        return self::$CASH_FLOW_NAMES[$type][0];
    }
    
    public function getInoutTypeCode($type)
    {      
        if (!isset(self::$CASH_FLOW_NAMES[$type])){
            return false;
        }
        
        return self::$CASH_FLOW_NAMES[$type][1];
    }
    
    public function getInoutTypeSign($type){
        
        if (!is_numeric($type) && is_string($type)){
            
            if (!isset(self::$CASH_FLOW_NAMES[$type])){
                return false;
            }
            
            return $this->getInoutTypeCode($type) == array_flip(self::$INOUT_TYPE)['Thu']
                    ? '+' : '-';
        }
        elseif (is_numeric($type)){

            if (!isset(self::$INOUT_TYPE[$type])){
                return false;
            }
            
            return $type == array_flip(self::$INOUT_TYPE)['Thu']
                    ? '+' : '-';
        }

        return false;
    }
    
    public function getFixCategoryCode($type)
    {
        if (!isset(self::$CASH_FLOW_NAMES[$type])){
            return false;
        }
        if (self::$CASH_FLOW_NAMES[$type][2]==0){
            return false;
        }
        
        return self::$CASH_FLOW_NAMES[$type][2];    
    }
}