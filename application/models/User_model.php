<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends App_Model {
    
    const TABLE = 'users';
    
    protected $columnNamesforSelectTagMethod = array('id', 'fullname');
    
    /*
     *--------------------------------------------------------------------
     * Kiểm tra xem password của user_id có match với dữ liệu trong db hay không.
     *
     * @param   string: password muốn kiểm tra
     * @param   int: user id
     * @return  boolean
     *--------------------------------------------------------------------
     */
    public function password_matched(string $password, int $user_id): bool
    {
        $user = $this->db
                     ->select('password')
                     ->where('id', $user_id)
                     ->limit(1)
                     ->get(self::TABLE)->row_array();
                     
        if (empty($user)) {
            return false;
        }
        
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        
        return true;
    }
    
    /*
     *--------------------------------------------------------------------
     * Thay đổi mật khẩu của user
     *
     * @param   string: mật khẩu mới
     * @param   int: user id
     *--------------------------------------------------------------------
     */
    public function change_password(string $password, int $user_id)
    {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $this->db->set('password', $password_hash)
                 ->where('id', $user_id)
                 ->update(self::TABLE);
    }
}