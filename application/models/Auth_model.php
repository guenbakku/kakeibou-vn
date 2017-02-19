<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends App_Model {
    
    const TABLE = 'users';
    
    public $user = [];
        
    protected $settings = [
        'login_attemps_max' => 5,
        'lock_duration_min' => 300, // seconds
    ];
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database('default');
    }
    
    public function verify(string $username, string $password): bool
    {
        try {
            if (empty($username) || strlen($username) > 32){
                throw new AppException(Consts::ERR_LOGIN_INFO_INVALID);
            }
            
            $this->user = $this->db->select('id, username, password, fullname, locked_on, lock_duration, login_attemps')
                            ->from(self::TABLE)
                            ->where('username', $username)
                            ->limit(1)
                            ->get()->row_array();
                            
            // Tài khoản không tồn tại
            if (empty($this->user)) {
                throw new AppException(Consts::ERR_LOGIN_INFO_INVALID);
            }
            
            // Tài khoản bị khóa
            if ($this->user['lock_duration'] > 0) {
                $locked_to = strtotime($this->user['locked_on']) + $this->user['lock_duration'];
                $now = time();
                if ($locked_to > $now) {
                    throw new AppException(sprintf(Consts::ERR_ACCOUNT_LOCKED, (int)(($locked_to - $now) / 60)));
                }
            }
            
            // Password không match
            if (!password_verify($password, $this->user['password'])) {
                $this->lockAccount();
                throw new AppException(Consts::ERR_LOGIN_INFO_INVALID);
            }
            
            $this->resetLockAccount();
            return true;
        } 
        catch (AppException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
    
    /*
     *--------------------------------------------------------------------
     * Xử lý khóa tài khoản nếu password bị sai
     *
     * @param   void
     * @return  void
     *--------------------------------------------------------------------
     */
    private function lockAccount()
    {
        $data = [];
        $data['login_attemps'] = $this->user['login_attemps'] + 1;
        if ($data['login_attemps'] % $this->settings['login_attemps_max'] === 0) {
            $data['locked_on'] = date('Y-m-d H:i:s');
            $data['lock_duration'] = $this->user['lock_duration'] > 0
                                     ? $this->user['lock_duration'] * 2
                                     : $this->settings['lock_duration_min'];
        }
        $this->db->where('id', $this->user['id'])
                 ->update(self::TABLE, $data);
    }
    
    /*
     *--------------------------------------------------------------------
     * Reset lại thông tin khóa tài khoản nếu đăng nhập thành công
     *
     * @param   array: dữ liệu user lấy từ db
     * @return  void
     *--------------------------------------------------------------------
     */
    private function resetLockAccount()
    {
        $data = array(
            'login_attemps' => 0,
            'locked_on'     => null,
            'lock_duration' => 0,
        );
        
        $this->db->where('id', $this->user['id'])
                 ->update(self::TABLE, $data);
    }
}    
