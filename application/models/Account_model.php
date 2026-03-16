<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Account_model extends App_Model
{
    // ID của Account Tiền mặt
    public const ACCOUNT_CASH_ID = 1;

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
     * Di chuyển tất cả các bản ghi từ tài khoản $from sang tài khoản $to, rồi xóa tài khoản $from.
     */
    public function move_records_and_delete(int $from, int $to)
    {
        $this->db->trans_start();
        $this->move_records($from, $to);
        $this->del($from);
        $this->db->trans_complete();
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
        if (!$this->is_empty($id)) {
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

    /**
     * Di chuyển tất cả các dữ liệu inout từ tài khoản $from sang tài khoản $to.
     */
    public function move_records(int $from, int $to)
    {
        if ($from == $to) {
            throw new AppException(sprintf(settings('err_account_move_from_to_same')));
        }
        if ($to === self::ACCOUNT_CASH_ID) {
            throw new AppException(sprintf(settings('err_account_move_to_cash_account')));
        }

        $this->db
            ->where('account_id', $from)
            ->update('inout_records', ['account_id' => $to])
        ;

        // Đối với những dữ liệu lưu động nội bộ (internal),
        // có khả năng account_to và account_from mỗi bên đang chứa 1 nửa của dữ liệu internal.
        // Trong trường hợp này, sau khi di chuyển hết dữ liệu sang account_to,
        // sẽ xảy ra trường hợp 2 bản ghi internal cùng trỏ về 1 account, cần được loại bỏ.
        // Dưới dây là xử lý xóa những dữ liệu internal như vậy.
        $sub_query = $this->db->select('ir_neg.pair_id')
            ->from('inout_records AS ir_neg')
            ->join(
                'inout_records AS ir_pos',
                'ir_neg.pair_id = ir_pos.pair_id
                    AND ir_neg.amount < 0
                    AND ir_pos.amount > 0
                    AND ir_neg.pair_id != ""
                    AND ir_pos.pair_id != ""'
            )
            ->where('ir_neg.account_id', $to)
            ->where('ir_pos.account_id', $to)
            ->where('ir_neg.cash_flow', 'internal')
            ->get_compiled_select()
        ;
        // ---
        $sql = "DELETE ir FROM inout_records ir
                INNER JOIN ({$sub_query}) AS to_delete
                ON ir.pair_id = to_delete.pair_id";
        // ---
        $this->db->query($sql);
    }

    /**
     * Kiểm tra xem tài khoản này có dữ liệu inout hay không.
     * Trả về true nếu rỗng, false nếu có dữ liệu.
     */
    public function is_empty(int $id): bool
    {
        return $this->db->where('account_id', $id)
            ->from('inout_records')
            ->count_all_results() == 0
        ;
    }
}
