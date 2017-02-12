<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_model extends CI_Model {
    
    // Tên column cần lấy dữ liệu để tạo tag HTML Select
    protected $columnNamesforSelectTagMethod = array('id', 'name');
    
    // Chứa lỗi xảy ra trong quá trình thực thi các model con
    protected $error = array();
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /*
     *--------------------------------------------------------------------
     * Lưu lại lỗi xảy ra 
     *
     *--------------------------------------------------------------------
     */
    public function setError($msg)
    {
        $this->error[] = $msg;
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
    public function getError($glue='<br>')
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