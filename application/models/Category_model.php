<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends App_Model {
    
    const TABLE = 'categories';
    
    /*
     *--------------------------------------------------------------------
     * Lấy thông tin category (theo list hoặc đơn lẻ)
     *
     * @param   mixed : null  => lấy hết table theo list
     *                  int   => lấy category đơn lẻ theo id
     * @return  void
     *--------------------------------------------------------------------
     */
    public function get(?int $id=null, array $where=array())
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
    
    /*
     *--------------------------------------------------------------------
     * Thêm một danh mục vào db
     *
     * @param   array: data của danh mục
     * @return  void
     *--------------------------------------------------------------------
     */
    public function add(array $data)
    {
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
     * Sửa danh mục
     *
     * @param   id: id của danh mục muốn sửa
     * @param   array: data mới của danh mục
     * @return  void
     *--------------------------------------------------------------------
     */
    public function edit(int $id, array $data)
    {
        $update_data = array(
            'name'              => $data['name'],
            'month_fixed_money' => $data['month_fixed_money'], 
        );
        
        $this->db->where('id', $id)->update(self::TABLE, $update_data);
    }

    /*
     *--------------------------------------------------------------------
     * Xóa danh mục khỏi db
     *
     * @param   id: id của danh mục muốn sửa
     * @return  void
     *--------------------------------------------------------------------
     */
    public function del(int $id)
    {
        $count = $this->db->where('category_id', $id)
                          ->from('inout_records')
                          ->count_all_results(self::TABLE);

        if ($count > 0){
            throw new AppException(Consts::ERR_CATEGORY_NOT_EMPTY);
        }
        
        $this->db->where('id', $id)->delete(self::TABLE);
    }
    
    /*
     *--------------------------------------------------------------------
     * Sửa lại thứ tự sắp xếp các danh mục
     *
     * @param   array   : array([id, order_no], [id, order_no]...)
     * @return  void
     *--------------------------------------------------------------------
     */
    public function editOrderNo(array $arr)
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
    
    /*
     *--------------------------------------------------------------------
     * Overide method 'getSelectTagData' trong App_Model
     *
     * @param   id: loại danh mục muốn lấy (thu: 1/chi: 2)
     * @return  array
     *--------------------------------------------------------------------
     */
    public function getSelectTagData($inout_type_id=null)
    {
        $this->db->where('inout_type_id', $inout_type_id)
                 ->where('restrict_delete', '0')
                 ->order_by('order_no', 'asc');
        
        return parent::getSelectTagData();
    }
}