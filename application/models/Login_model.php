<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends App_Model {
    
    const TABLE        = 'users';
    const COOKIE_NAME  = 'bhcb_auth';
    const SESSION_NAME = 'bhcb_auth';
    const LOGIN_URL    = 'user/login';
    const LOGIN_ATTEMPS_MAX = 5;
    const LOCK_INTERVAL_MIN = 300; // 300 giây
    
    protected $user = array(); // Thông tin login của User lấy từ DB
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database('default');
        $this->recoverSession();
    }
    
    /*
     *--------------------------------------------------------------------
     * Thực thi thao tác Login
     * - Kiểm tra Username & Password
     * - Thiết lập kết nối Cookie & Session
     *
     *--------------------------------------------------------------------
     */
    public function excuteLogin($username, $password, $remember)
    {
        if ($this->validate($username, hash('sha512', $password))){
            $this->setConnection($remember);
            $this->setSession();
            return true;
        }
        else {
            return false;
        }
    }
    
    /*
     *--------------------------------------------------------------------
     * Kiểm tra có đang login hay không
     *
     *--------------------------------------------------------------------
     */
    public function isLogin()
    { 
        return $this->session->userdata(self::SESSION_NAME) === null? false : true;
    }
    
    /*
     *--------------------------------------------------------------------
     * Xóa kết nối đăng nhập khi Logout
     *
     *--------------------------------------------------------------------
     */
    public function delConnection()
    {
        $this->input->set_cookie(array(
            'name'      => self::COOKIE_NAME,
            'expire'    => -1,
        ));
        
        unset($_SESSION[self::SESSION_NAME]);
    }
    
    /*
     *--------------------------------------------------------------------
     * Kiểm tra thông tin login
     * Nếu đúng với thông tin trong CSDL thì trả về True, ngược lại trả về false
     * 
     *--------------------------------------------------------------------
     */
    private function validate($username, $password)
    {   
        try {
            
            if (empty($username) || strlen($username) > 32){
                throw new Exception(Constants::ERR_LOGIN_INFO_INVALID);
            }
            
            $user = $this->db->select('id, username, password, fullname, locked_on, lock_duration, login_attemps')
                               ->from(self::TABLE)
                               ->where('username', $username)
                               ->limit(1)
                               ->get()->row_array();
                               
            // Tài khoản không tồn tại
            if (empty($user)){
                throw new Exception(Constants::ERR_LOGIN_INFO_INVALID);
            }
            
            // Tài khoản bị khóa
            if ($user['lock_duration'] > 0) {
                $locked_to = strtotime($user['locked_on']) + $user['lock_duration'];
                $current = time();
                if ($locked_to > $current) {
                    throw new Exception(sprintf(Constants::ERR_ACCOUNT_LOCKED, (int)(($locked_to - $current) / 60)));
                }
            }
            
            // Password không chính xác
            if ($user['password'] !== $password){
                $this->lockAccount($user);
                throw new Exception(Constants::ERR_LOGIN_INFO_INVALID);
            }
            
            $this->saveDbInfo($user);
            $this->resetLockAccount($user);
            
            return true;
        }
        catch (Exception $e){
            $this->setError($e->getMessage());
            return false;
        }
        
    }
    
    /*
     *--------------------------------------------------------------------
     * Xử lý khóa tài khoản nếu password bị sai
     *
     * @param   array: dữ liệu user lấy từ db
     * @return  void
     *--------------------------------------------------------------------
     */
    private function lockAccount($user)
    {
        $data = array();
        $data['login_attemps'] = $user['login_attemps']+1;
        if ($data['login_attemps'] % self::LOGIN_ATTEMPS_MAX == 0) {
            $data['locked_on'] = date('Y-m-d H:i:s');
            $data['lock_duration'] = $user['lock_duration']
                                     ? $user['lock_duration'] * 2
                                     : self::LOCK_INTERVAL_MIN;
        }
        $this->db->where('id', $user['id'])
                 ->set($data)
                 ->update(self::TABLE);
    }
    
    /*
     *--------------------------------------------------------------------
     * Reset lại thông tin khóa tài khoản nếu đăng nhập thành công
     *
     * @param   array: dữ liệu user lấy từ db
     * @return  void
     *--------------------------------------------------------------------
     */
    private function resetLockAccount($user)
    {
        $data = array(
            'login_attemps' => 0,
            'locked_on'     => null,
            'lock_duration' => 0,
        );
        
        $this->db->where('id', $user['id'])
                 ->set($data)
                 ->update(self::TABLE);
    }
    
    /*
     *--------------------------------------------------------------------
     * Thiết lập giá trị cho biến $this->info
     *
     *--------------------------------------------------------------------
     */
    private function saveDbInfo($array)
    {
        foreach (array('id', 'username', 'password', 'fullname') as $key){
            $this->user[$key] = isset($array[$key])? $array[$key] : null;
        }
    }
    
    /*
     *--------------------------------------------------------------------
     * Tạo cookie để lưu kết nối đăng nhập
     *
     *--------------------------------------------------------------------
     */
    private function setConnection($remember=false)
    {   
        try {
            
            if (empty($this->user)){
                throw new Exception('Biến user rỗng');
            }
            
            $this->load->library('encryption');
            
            $infoJson = json_encode(array(
                'username' => $this->user['username'], 
                'password' => $this->user['password'],
            ));
            $infoJsonEncrypt = $this->encryption->encrypt($infoJson);
            
            $this->input->set_cookie(array(
                'name'      => self::COOKIE_NAME,
                'value'     => $infoJsonEncrypt,
                'expire'    => $remember==false? 0 : 31536000, // 365 ngày
            ));
            
        }
        catch (Exception $e){
            show_error($e->getMessage());
        }
        
    }
    
    /*
     *--------------------------------------------------------------------
     * Khôi phục lại session chứa thông tin user đăng nhập dựa vào 
     * Cookie lưu kết nối đăng nhập
     *
     *--------------------------------------------------------------------
     */
    private function recoverSession()
    {
       
        $infoJsonEncrypt = $this->input->cookie(self::COOKIE_NAME);
        
        if ($this->session->userdata(self::SESSION_NAME) === null && $infoJsonEncrypt !== null){

            $this->load->library('encryption');
            
            $infoJson = $this->encryption->decrypt($infoJsonEncrypt);
            $info     = json_decode($infoJson, true);
            
            if (isset($info['username'], $info['password']) 
                && $this->validate($info['username'], $info['password'])){
                
                $this->setSession();
            }
        }
    }
    
    /*
     *--------------------------------------------------------------------
     * Lưu thông tin của User đang đăng nhập vào Session để sử dụng 
     * cho các xử lý sau
     *
     *--------------------------------------------------------------------
     */
    private function setSession()
    {   
        try {
            
            if (empty($this->user)){
                throw new Exception('Biến user rỗng');
            }
            
            $this->session->set_userdata(self::SESSION_NAME, $this->user);
            
        }
        catch (Exception $e){
            show_error($e->getMessage());
        }
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy thông tin của User đang đăng nhập từ SESSION
     *
     * @param   string : key name or null
     * @return  mixed  : value of key or array of false if $key is incorrect
     *--------------------------------------------------------------------
     */
    public function getInfo($key=null)
    {
        $loginInfo = $this->session->userdata(self::SESSION_NAME);
        if ($key === null){
            return $loginInfo;
        }
        elseif (is_string($key)){
            return isset($loginInfo[$key])? $loginInfo[$key] : false;
        }
        else {
            return false;
        }
    }
    
    /*
     *--------------------------------------------------------------------
     * Trả về url đến trang đăng nhập
     *
     * @param   void
     * @return  string
     *--------------------------------------------------------------------
     */
    public function getLoginUrl()
    {
        return base_url().self::LOGIN_URL;
    }
    
}
