<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends App_Model {
    
    const TABLE        = 'users';
    const COOKIE_NAME  = 'bhcb_loginAuth';
    const SESSION_NAME = 'bhcb_loginAuth';
    
    public $dbInfo    = array(); // Thông tin login của User lấy từ DB
    
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
                throw new Exception('Username không hợp lệ');
            }
            
            $dbInfo = $this->db->select('uid, username, password, fullname')
                               ->from(self::TABLE)
                               ->where('username', $username)
                               ->limit(1)
                               ->get()->row_array();
                            
            if (empty($dbInfo)){
                throw new Exception('Username không tồn tại');
            }
            
            if ($dbInfo['password'] !== $password){
                throw new Exception('Password không đúng');
            }
            
            $this->saveDbInfo($dbInfo);
            
            return true;
        }
        catch (Exception $e){
            $this->setError($e->getMessage());
            return false;
        }
        
    }
    
    /*
     *--------------------------------------------------------------------
     * Thiết lập giá trị cho biến $this->info
     *
     *--------------------------------------------------------------------
     */
    private function saveDbInfo($array)
    {
        foreach (array('uid', 'username', 'password', 'fullname') as $key){
            $this->dbInfo[$key] = isset($array[$key])? $array[$key] : null;
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
            
            if (empty($this->dbInfo)){
                throw new Exception('Biến dbInfo rỗng');
            }
            
            $this->load->library('encryption');
            
            $infoJson = json_encode(array(
                'username' => $this->dbInfo['username'], 
                'password' => $this->dbInfo['password'],
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
            
            if (empty($this->dbInfo)){
                throw new Exception('Biến dbInfo rỗng');
            }
            
            $this->session->set_userdata(self::SESSION_NAME, $this->dbInfo);
            
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
    
}