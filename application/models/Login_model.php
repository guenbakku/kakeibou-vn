<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model {
    
    public $dbInfo    = array();                    // Thông tin của user đang login lấy từ DB
    public $db;
    private $err  = '';
    private $cookieName  = 'bhcb_loginAuth';
    private $sessionName = 'bhcb_loginAuth';
    
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->db = $this->load->database('default', true);
        $this->recoverLoginSession();
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
        if ($this->checkLoginInfo($username, hash('sha512', $password))){
            $this->setLoginConn($remember);
            $this->setLoginSession();
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
        return $this->session->userdata($this->sessionName) === null? false : true;
    }
    
    /*
     *--------------------------------------------------------------------
     * Xóa kết nối đăng nhập khi Logout
     *
     *--------------------------------------------------------------------
     */
    public function delLoginConn()
    {
        $this->input->set_cookie(array(
            'name'      => $this->cookieName,
            'expire'    => -1,
        ));
        
        $this->session->sess_destroy();
    }
    
    /*
     *--------------------------------------------------------------------
     * Kiểm tra thông tin login
     * Nếu đúng với thông tin trong CSDL thì trả về True, ngược lại trả về false
     * 
     *--------------------------------------------------------------------
     */
    private function checkLoginInfo($username, $password)
    {   
        
        try {
            
            if (empty($username) || strlen($username) > 32){
                throw new Exception('Username không hợp lệ');
            }
            
            $dbInfo = $this->db->select('uid, username, password')
                               ->from('users')
                               ->where('username', $username)
                               ->limit(1)
                               ->get()->row_array();
                            
            if (empty($dbInfo)){
                throw new Exception('Username không tồn tại');
            }
            
            if ($dbInfo['password'] !== $password){
                throw new Exception('Password không đúng');
            }
            
            $this->saveDbInfo($dbInfo['uid'], $dbInfo['username'], $dbInfo['password']);
            
            return true;
        }
        catch (Exception $e){
            $this->setErr($e->getMessage());
            return false;
        }
        
    }
    
    /*
     *--------------------------------------------------------------------
     * Thiết lập giá trị cho biến $this->info
     *
     *--------------------------------------------------------------------
     */
    private function saveDbInfo($uid, $username, $password)
    {
        $this->dbInfo['uid'] = $uid;
        $this->dbInfo['username'] = $username;
        $this->dbInfo['password'] = $password;
    }
    
    /*
     *--------------------------------------------------------------------
     * Tạo cookie để lưu kết nối đăng nhập
     *
     *--------------------------------------------------------------------
     */
    private function setLoginConn($remember=false)
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
                'name'      => $this->cookieName,
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
    private function recoverLoginSession()
    {
       
        $infoJsonEncrypt = $this->input->cookie($this->cookieName);
        
        if ($this->session->userdata($this->sessionName) === null && $infoJsonEncrypt !== null){

            $this->load->library('encryption');
            
            $infoJson = $this->encryption->decrypt($infoJsonEncrypt);
            $info     = json_decode($infoJson, true);
            
            if (isset($info['username'], $info['password']) 
                && $this->checkLoginInfo($info['username'], $info['password'])){
                
                $this->setLoginSession();
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
    private function setLoginSession()
    {   
        try {
            
            if (empty($this->dbInfo)){
                throw new Exception('Biến dbInfo rỗng');
            }
            
            $this->session->set_userdata($this->sessionName, $this->dbInfo);
            
        }
        catch (Exception $e){
            show_error($e->getMessage());
        }
    }
    
    /*
     *--------------------------------------------------------------------
     *
     *
     *--------------------------------------------------------------------
     */
    public function setErr($msg)
    {
        $this->err .= $msg.'<br>';
    }
    
    /*
     *--------------------------------------------------------------------
     *
     *
     *--------------------------------------------------------------------
     */
    public function getErr()
    {
        return $this->err;
    }
}