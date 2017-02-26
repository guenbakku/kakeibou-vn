<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends App_Model {
    
    const TABLE = 'categories';
    
    public function add($data){
        
        $order_no = current($this->db->select_max('order_no')
                                     ->where('inout_type_id', $data['inout_type_id'])
                                     ->where('restrict_delete', 0)
                                     ->get(self::TABLE)->row_array()) + 1;
        
        $add_data = array(
            'name'              => $data['name'],
            'inout_type_id'     => $data['inout_type_id'],
            'month_fixed_money' => $data['month_fixed_money'],
            'order_no'          => $order_no,
        );
        
        $this->db->insert(self::TABLE, $add_data);
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy thông tin category (theo list hoặc đơn lẻ)
     *
     * @param   mixed : null  => lấy hết table theo list
     *                  int   => lấy category đơn lẻ theo id
     * @return  void
     *--------------------------------------------------------------------
     */
    public function get($id=null, $where=array())
    {
        if (is_numeric($id)){
            $this->db->where('id', $id);
        }
        
        if (!isset($where['restrict_delete'])){
            $where['restrict_delete'] = 0;
        }
        
        $res = $this->db->where($where)
                    ->order_by('order_no ASC')
                    ->get(self::TABLE);
        
        return is_numeric($id)? $res->row_array() : $res->result_array();
    }
    
    public function edit($id, $data)
    {
        $update_data = array(
            'name'              => $data['name'],
            'month_fixed_money' => $data['month_fixed_money'], 
        );
        
        $this->db->where('id', $id)->update(self::TABLE, $update_data);
    }
    
    /*
     *--------------------------------------------------------------------
     * Sửa lại thứ tự sắp xếp các danh mục
     *
     * @param   array   : array([id, order_no], [id, order_no]...)
     * @return  void
     *--------------------------------------------------------------------
     */
    public function editOrderNo($arr)
    {
        $update_data = array();
        foreach($arr as $i => $item){
            if(isset($item['id'], $item['order_no'])){
                $update_data[] = array(
                    'id'                => $item['id'],
                    'order_no'          => $item['order_no'],
                    'month_fixed_money' => isset($item['month_fixed_money'])
                                           ? (bool)$item['month_fixed_money'] 
                                           : false,
                );
            }
        }
        $this->db->update_batch(self::TABLE, $update_data, 'id');
    }
    
    public function del($id)
    {
        $count = $this->db->where('category_id', $id)
                          ->from('inout_records')
                          ->count_all_results(self::TABLE);

        if ($count > 0){
            throw new AppException(Consts::ERR_CATEGORY_NOT_EMPTY);
        }
        
        $this->db->where('id', $id)->delete(self::TABLE);
    }
    
    public function getSelectTagData($inout_type_id=null)
    {
        $this->db->where('inout_type_id', $inout_type_id)
                 ->where('restrict_delete', '0')
                 ->order_by('order_no', 'asc');
        
        return parent::getSelectTagData();
    }
}