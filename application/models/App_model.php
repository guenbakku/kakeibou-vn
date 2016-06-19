<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class App_model extends CI_Model {
    
    protected $error = array(); // Chứa lỗi xảy ra trong quá trình thực thi các model con
            
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
     * Lấy dữ liệu từ CSDL để xuất select tag
     *
     * @param   string  : name của select tag
     * @return  array   : dữ liệu để xuất option
     *--------------------------------------------------------------------
     */
    public function getSelectTagData($name=null, $option=null)
    {
        $select = array();
        $table  = ''; 
        switch ($name){
            case 'account_id':
                $select = array('aid', 'name');
                $table  = 'accounts';
                break;
            case 'user_id':
                $select = array('uid', 'fullname');
                $table  = 'users';
                break;
            case 'category_id':
                $select = array('cid', 'name');
                $table  = 'categories';
                $this->db->where('inout_type_id', $option);
                $this->db->where('restrict_delete', '0');
                break;
            case 'yearsInDB': 
                $yearList = $this->getYearsListInDB();
                return array_combine($yearList, $yearList);
            default:
                return false;
        }
        
        return array_column(
                        $this->db->select($select)
                                 ->get($table)->result_array(), 
                        $select[1],
                        $select[0] 
                    );
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy danh sách tất cả năm có trong table inout_record
     * 
     * @param   void
     * @return  array
     *--------------------------------------------------------------------
     */
    public function getYearsListInDB()
    {
        $table = 'inout_records';
        $range = $this->db->select("DATE_FORMAT(MIN(`date`), '%Y') as `min`, 
                                    DATE_FORMAT(MAX(`date`), '%Y') as `max`", false)
                          ->get($table)->row_array();
                          
        return $full_list = array_map(function($year){
                    return sprintf('%04d', $year);
               }, range($range['min'], $range['max']));
    }
}