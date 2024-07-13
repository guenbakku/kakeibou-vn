<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Account_model extends App_Model
{
    public const TABLE = 'accounts';

    /**
     * Lấy danh sách tài khoản.
     *
     * @param   mixed : null  => lấy hết table theo list
     *                  int   => lấy account có id cụ thể
     * @param   array: điều kiện search
     *
     * @return array
     */
    public function get(?int $id = null, array $where = [])
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
     * Thêm mới tài khoản.
     *
     * @param   array: data của tài khoản
     */
    public function add(array $account)
    {
        $order_no = current($this->db->select_max('order_no')
            ->where('restrict_delete', 0)
            ->get(self::TABLE)->row_array()) + 1;

        $add_data = [
            'name' => $account['name'],
            'description' => $account['description'],
            'order_no' => $order_no,
            'icon' => 'fa-bank',
        ];

        $this->db->insert(self::TABLE, $add_data);
    }

    /**
     * Sửa tài khoản.
     *
     * @param   id: id của tài khoản muốn sửa
     * @param   array: data mới của tài khoản
     */
    public function edit(int $id, array $account)
    {
        $update_data = [
            'name' => $account['name'],
            'description' => $account['description'],
        ];

        $this->db->where('id', $id)->update(self::TABLE, $update_data);
    }

    /**
     * Edit batch.
     *
     * @param   array: dữ liệu muốn update
     * @param   string: column làm chuẩn
     */
    public function edit_batch(array $accounts, string $primary = 'id')
    {
        $this->db->update_batch(self::TABLE, $accounts, $primary);
    }

    /**
     * Xóa tài khoản khỏi db.
     *
     * @param   id: id của tài khoản muốn xóa
     */
    public function del(int $id)
    {
        $account_name = $this->db->select('name')
            ->where('id', $id)
            ->get(self::TABLE)->row_array()['name']
        ;

        // Kiểm tra xem danh mục này có chứa dữ liệu thu chi nào không
        $count = $this->db->where('account_id', $id)
            ->from('inout_records')
            ->count_all_results()
        ;
        if ($count > 0) {
            throw new AppException(sprintf(Consts::ERR_ACCOUNT_NOT_EMPTY, $account_name));
        }

        // Kiểm tra xem danh mục này có phải danh mục cấm xóa không
        $count = $this->db->where('id', $id)
            ->where('restrict_delete', 1)
            ->from(self::TABLE)
            ->count_all_results()
        ;
        if ($count > 0) {
            throw new AppException(sprintf(Consts::ERR_ACCOUNT_RESTRICT_DELETE, $account_name));
        }

        $this->db->where('id', $id)->delete(self::TABLE);
    }
}
