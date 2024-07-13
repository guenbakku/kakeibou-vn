<?php

defined('BASEPATH') or exit('No direct script access allowed');

class App_model extends CI_Model
{
    // Tên column cần lấy dữ liệu để tạo tag HTML Select
    protected $select_tag_columns = ['id', 'name'];

    // Chứa lỗi xảy ra trong quá trình thực thi các model con
    protected $error = [];

    // Chứa những settings cần thiết cho model
    protected $settings = [];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Merge settings của model với thông tin được truyền vào.
     *
     * @param   array: setting
     *
     * @return  object: $this
     */
    public function config(array $settings)
    {
        $this->settings = array_update($this->settings, $settings);

        return $this;
    }

    /**
     * Lấy các lỗi xảy ra.
     *
     * @param   string : ký tự để nối các lỗi thành 1 chuỗi.
     *                   Nếu truyền false sẽ trả về nguyên array
     *
     * @return  string/array
     */
    public function get_error(string $glue = '<br>')
    {
        return false === $glue ? $this->error : implode($glue, $this->error);
    }

    /**
     * Lấy dữ liệu từ CSDL để tạo select tag.
     *
     * @param   void
     *
     * @return array : dữ liệu để xuất option
     */
    public function get_select_tag_data()
    {
        $select = $this->select_tag_columns;
        $table = $this::TABLE;

        if ($this->db->field_exists('order_no', $table)) {
            $this->db->order_by('order_no', 'asc');
        }

        return array_column(
            $this->db->select($select)
                ->order_by($select[0], 'asc')
                ->get($table)->result_array(),
            $select[1],
            $select[0]
        );
    }

    /**
     * Xóa những field không có trong db trước khi lưu data vào db.
     *
     * @param   array: dữ liệu muốn lưu vào db
     * @param mixed $data
     *
     * @return  array: dữ liệu sau khi đã bỏ những field ko cần thiết
     */
    public function remove_garbage_fields($data)
    {
        $whitelist = $this->db->list_fields(static::TABLE);
        $whitelist = array_flip($whitelist);
        foreach ($data as $field => $val) {
            if (!isset($whitelist[$field])) {
                unset($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Lưu lại lỗi xảy ra.
     *
     * @param   string: thông tin muốn lưu
     *
     * @return  object: $this
     */
    protected function set_error(string $msg)
    {
        $this->error[] = $msg;

        return $this;
    }
}
