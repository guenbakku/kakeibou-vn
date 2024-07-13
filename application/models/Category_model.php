<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category_model extends App_Model
{
    public const TABLE = 'categories';

    /**
     * Lấy thông tin category (theo list hoặc đơn lẻ).
     *
     * @param null|int $id
     *                        - null  => lấy hết table theo list
     *                        - int   => lấy category đơn lẻ theo id
     * @param array    $where điều kiện search
     */
    public function get(?int $id = null, array $where = []): array
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        }

        if (!isset($where['restrict_delete'])) {
            $where['restrict_delete'] = 0;
        }

        $res = $this->db->where($where)
            ->order_by('order_no', 'asc')
            ->get(self::TABLE)
        ;

        return is_numeric($id) ? $res->row_array() : $res->result_array();
    }

    /**
     * Thêm một danh mục vào db.
     */
    public function add(array $category)
    {
        $order_no = current($this->db->select_max('order_no')
            ->where('inout_type_id', $category['inout_type_id'])
            ->where('restrict_delete', 0)
            ->get(self::TABLE)->row_array()) + 1;

        $add_data = [
            'name' => $category['name'],
            'inout_type_id' => $category['inout_type_id'],
            'order_no' => $order_no,
        ];

        $this->db->insert(self::TABLE, $add_data);
    }

    /**
     * Sửa danh mục.
     */
    public function edit(int $id, array $category)
    {
        $update_data = [
            'name' => $category['name'],
            'is_month_fixed_money' => $category['is_month_fixed_money'],
        ];

        $this->db->where('id', $id)->update(self::TABLE, $update_data);
    }

    /**
     * Xóa danh mục khỏi db.
     */
    public function del(int $id)
    {
        $category_name = $this->db->select('name')
            ->where('id', $id)
            ->get(self::TABLE)->row_array()['name']
        ;

        // Kiểm tra xem danh mục này có chứa dữ liệu thu chi nào không
        $count = $this->db->where('category_id', $id)
            ->from('inout_records')
            ->count_all_results()
        ;
        if ($count > 0) {
            throw new AppException(sprintf(Consts::ERR_CATEGORY_NOT_EMPTY, $category_name));
        }

        // Kiểm tra xem danh mục này có phải danh mục cấm xóa không
        $count = $this->db->where('id', $id)
            ->where('restrict_delete', 1)
            ->from(self::TABLE)
            ->count_all_results()
        ;
        if ($count > 0) {
            throw new AppException(sprintf(Consts::ERR_CATEGORY_RESTRICT_DELETE, $category_name));
        }

        $this->db->where('id', $id)->delete(self::TABLE);
    }

    /**
     * Edit batch.
     *
     * @param array  $categories dữ liệu muốn update
     * @param string $primary    column làm chuẩn
     */
    public function edit_batch(array $categories, string $primary = 'id')
    {
        $this->db->update_batch(self::TABLE, $categories, $primary);
    }

    /**
     * Lấy dữ liệu dự định chi trong tháng này.
     */
    public function get_month_estimated_outgo(): array
    {
        return $this->db->select('SUM(`month_estimated_amount`) as `total`', false)
            ->select('SUM(CASE WHEN `is_month_fixed_money` = 0 THEN `month_estimated_amount` ELSE 0 END) AS `liquid`', false)
            ->select('SUM(CASE WHEN `is_month_fixed_money` = 1 THEN `month_estimated_amount` ELSE 0 END) AS `fixed`', false)
            ->where('inout_type_id', array_flip($this->inout_model::$INOUT_TYPE)['Chi'])
            ->get(self::TABLE)->row_array()
        ;
    }

    /**
     * Kiểm tra category có phải là loại thu chi cố định hàng tháng hay không.
     *
     * @param int $id id của category
     *
     * @return null|bool true nếu là loại thu chi cố định hàng tháng và ngược lại.
     *                   null nếu id không tồn tại trong CSDL.
     */
    public function is_month_fixed_money(int $id): ?bool
    {
        $result = $this->db->select('is_month_fixed_money')
            ->where('id', $id)
            ->get(self::TABLE)->row_array()
        ;

        if (null === $result) {
            return null;
        }

        return (bool) $result['is_month_fixed_money'];
    }

    /**
     * Override method 'get_select_tag_data' trong App_Model.
     *
     * @param int $inout_type_id loại danh mục muốn lấy
     *                           - 1: thu
     *                           - 2: chi
     *                           - null: lấy tất cả dữ liệu
     */
    public function get_select_tag_data(?int $inout_type_id = null): array
    {
        $this->db->where('inout_type_id', $inout_type_id)
            ->where('restrict_delete', 0)
            ->order_by('order_no', 'asc')
        ;

        return parent::get_select_tag_data();
    }
}
