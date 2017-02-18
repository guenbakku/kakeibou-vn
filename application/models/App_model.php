<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_model extends CI_Model {
    
    // Tên column cần lấy dữ liệu để tạo tag HTML Select
    protected $columnNamesforSelectTagMethod = array('id', 'name');
    
    // Chứa lỗi xảy ra trong quá trình thực thi các model con
    protected $error = [];
    
    // Chứa những settings cần thiết cho model
    protected $settings = [];
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /*
     *--------------------------------------------------------------------
     * Merge settings của model với thông tin được truyền vào 
     *
     * @param   array: setting
     * @return  object: $this
     *--------------------------------------------------------------------
     */
    public function config(array $settings)
    {   
        $this->settings = array_update($this->settings, $settings);
        return $this;
    }
    
    /*
     *--------------------------------------------------------------------
     * Lưu lại lỗi xảy ra 
     *
     * @param   string: thông tin muốn lưu
     * @return  object: $this
     *--------------------------------------------------------------------
     */
    protected function setError(string $msg)
    {
        $this->error[] = $msg;
        return $this;
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy các lỗi xảy ra
     * 
     * @param   string : ký tự để nối các lỗi thành 1 chuỗi. 
     *                   Nếu truyền false sẽ trả về nguyên array
     *
     * @return  string/array
     *--------------------------------------------------------------------
     */
    public function getError(string $glue='<br>')
    {
        return $glue===false? $this->error : implode($glue, $this->error);
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy dữ liệu từ CSDL để tạo select tag
     *
     * @param   void
     * @return  array : dữ liệu để xuất option
     *--------------------------------------------------------------------
     */    
    public function getSelectTagData()
    {
        $select = $this->columnNamesforSelectTagMethod;
        $table  = $this::TABLE;
        
        return array_column(
            $this->db->select($select)
                     ->order_by($select[0], 'asc')
                     ->get($table)->result_array(), 
            $select[1],
            $select[0] 
        );
    }
}