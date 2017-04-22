<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends App_Model {
    
    const TABLE = 'users';
    
    protected $select_tag_columns = array('id', 'fullname');
    
    /*
     *--------------------------------------------------------------------
     * Lấy thông tin của user
     *
     * @param   int: user id
     * @param   array: field muốn lấy
     * @return  array: thông tin của user
     *--------------------------------------------------------------------
     */
    public function get(int $user_id, array $fields=[]): array
    {
        if (empty($fields)) {
            $fields = ['id', 'username', 'fullname'];
        }
        return $this->db
                    ->select($fields)
                    ->where('id', $user_id)
                    ->limit(1)
                    ->get(self::TABLE)->row_array();
    }
    
    /*
     *--------------------------------------------------------------------
     * Update thông tin của user
     *
     * @param   int: user id
     * @param   array: data để updata
     * @return  void
     *--------------------------------------------------------------------
     */
    public function edit(int $user_id, array $data)
    {
        unset($data['id']);
        
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        $this->db
             ->set($data)
             ->where('id', $user_id)
             ->update(self::TABLE);
    }
    
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
}