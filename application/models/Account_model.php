<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Account_model extends App_Model
{
    public function get_table(): string
    {
        return 'accounts';
    }

    /**
     * Lấy danh sách tài khoản.
     *
     * @param null|int $id
     *                        - null  => lấy hết table theo list
     *                        - int   => lấy account có id cụ thể
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
            ->get($this->get_table())
        ;

        return is_numeric($id) ? $res->row_array() : $res->result_array();
    }

    /**
     * Thêm mới tài khoản.
     */
    public function add(array $account)
    {
        $order_no = current($this->db->select_max('order_no')
            ->where('restrict_delete', 0)
            ->get($this->get_table())->row_array()) + 1;

        $add_data = [
            'name' => $account['name'],
            'description' => $account['description'],
            'order_no' => $order_no,
            'icon' => 'fa-bank',
        ];

        $this->db->insert($this->get_table(), $add_data);
    }

    /**
     * Sửa tài khoản.
     */
    public function edit(int $id, array $account)
    {
        $update_data = [
            'name' => $account['name'],
            'description' => $account['description'],
        ];

        $this->db->where('id', $id)->update($this->get_table(), $update_data);
    }

    /**
     * Edit batch.
     *
     * @param array  $accounts dữ liệu muốn update
     * @param string $primary  column làm chuẩn
     */
    public function edit_batch(array $accounts, string $primary = 'id')
    {
        $this->db->update_batch($this->get_table(), $accounts, $primary);
    }

    /**
     * Xóa tài khoản khỏi db.
     */
    public function del(int $id)
    {
        $account_name = $this->db->select('name')
            ->where('id', $id)
            ->get($this->get_table())->row_array()['name']
        ;

        // Kiểm tra xem danh mục này có chứa dữ liệu thu chi nào không
        $count = $this->db->where('account_id', $id)
            ->from('inout_records')
            ->count_all_results()
        ;
        if ($count > 0) {
            throw new AppException(sprintf(settings('err_account_not_empty'), $account_name));
        }

        // Kiểm tra xem danh mục này có phải danh mục cấm xóa không
        $count = $this->db->where('id', $id)
            ->where('restrict_delete', 1)
            ->from($this->get_table())
            ->count_all_results()
        ;
        if ($count > 0) {
            throw new AppException(sprintf(settings('err_account_restrict_delete'), $account_name));
        }

        $this->db->where('id', $id)->delete($this->get_table());
    }
}
