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
    public function get(?int $id=null, array $where=[])
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
    public function add(array $category)
    {
        $order_no = current($this->db->select_max('order_no')
                                     ->where('inout_type_id', $category['inout_type_id'])
                                     ->where('restrict_delete', 0)
                                     ->get(self::TABLE)->row_array()) + 1;
        
        $add_data = array(
            'name'              => $category['name'],
            'inout_type_id'     => $category['inout_type_id'],
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
    public function edit(int $id, array $category)
    {
        $update_data = array(
            'name'              => $category['name'],
            'month_fixed_money' => $category['month_fixed_money'], 
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
            $category_name = $this->db->select('name')
                                      ->where('id', $id)
                                      ->get(self::TABLE)->row_array()['name'];
            
            throw new AppException(sprintf(Consts::ERR_CATEGORY_NOT_EMPTY, $category_name));
        }
        
        $this->db->where('id', $id)->delete(self::TABLE);
    }
    
    /*
     *--------------------------------------------------------------------
     * Edit batch
     * 
     * @param   array: dữ liệu muốn update
     * @param   string: column làm chuẩn
     * @return  void
     *--------------------------------------------------------------------
     */
    public function edit_batch(array $categories, string $primary = 'id')
    {
        $this->db->update_batch(self::TABLE, $categories, $primary);
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy dữ liệu dự định chi trong tháng này
     * 
     * @param   void
     * @return  array
     *--------------------------------------------------------------------
     */
    public function get_month_estimated_outgo(): array
    {
        return $this->db->select("SUM(`month_estimated_inout`) as `total`", false)
                        ->select("SUM(CASE WHEN `month_fixed_money` = 0 THEN `month_estimated_inout` ELSE 0 END) AS `liquid`", false)
                        ->select("SUM(CASE WHEN `month_fixed_money` = 1 THEN `month_estimated_inout` ELSE 0 END) AS `fixed`", false)
                        ->where('inout_type_id', array_flip($this->inout_model::$INOUT_TYPE)['Chi'])
                        ->get(self::TABLE)->row_array();
    }
    
    /*
     *--------------------------------------------------------------------
     * Overide method 'get_select_tag_data' trong App_Model
     *
     * @param   id: loại danh mục muốn lấy (thu: 1/chi: 2)
     * @return  array
     *--------------------------------------------------------------------
     */
    public function get_select_tag_data(int $inout_type_id=null)
    {
        $this->db->where('inout_type_id', $inout_type_id)
                 ->where('restrict_delete', '0')
                 ->order_by('order_no', 'asc');
        
        return parent::get_select_tag_data();
    }
}