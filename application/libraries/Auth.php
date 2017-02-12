<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CodeIgniter Authentication & Authorization Class
 *
 * This class contains process which handle authentication and authorization.
 * This class need model auth_model for communicating with database.
 *
 * @package		CodeIgniter
 * @author		Nguyen Van Bach
 * @subpackage	Libraries
 * @category	Libraries
 * @link		http://nvb-online.com
 * @copyright   Copyright (c) 2017, Nguyen Van Bach.
 * @license     MIT
 * @version 0.0.1
 */
class Auth {
   
    protected $CI;
    
    public $cookie_name = 'bhcb_auth';
    public $session_name = 'bhcb_auth_test';
    public $remember_table = 'remember';
    public $error = '';
    
    // Danh sách controller và action cho phép truy cập không cần authenticate
    // Format: [
    //      'controller1' => [action1, action2],
    // ]
    public $allowed = [
        'user' => ['login'],
    ];
    
    // Số lần nhập sai mật khẩu liên tiếp tối đa.
    // Nếu số lần nhập sai mật khẩu liên tiếp vượt quá giá trị này, 
    // tài khoản sẽ bị khóa.
    public $login_attemps_max = 5;
    
    // Thời gian khóa tài khoản tối thiểu.
    // Thời gian này sẽ tăng theo cấp số cộng nếu sau khi hết thời gian khóa
    // vẫn nhập sai mật khẩu.
    public $lock_duration_min = 300;
    
    protected $verify_info = [
        'username'  => null,
        'password'  => null,
        'remember'  => false,
    ];
    
    // Token của session đăng nhập hiện tại
    protected $token = null;
    
    public function __construct()
    {
        // Copy an instance of CI so we can use the entire framework.
        $this->CI =& get_instance();
        $this->CI->load->library('session');
        $this->CI->load->helper('string');
        $this->CI->load->model('auth_model');
        
        $this->CI->auth_model->config([
            'login_attemps_max' => $this->login_attemps_max,
            'lock_duration_min' => $this->lock_duration_min,
        ]);
    }
    
    public function set_verify_info(array $verify_info) {
        $this->verify_info = array_update($this->verify_info, $verify_info);
        return $this;
    }
    
    public function authenticate(): bool
    {
        $username = $this->verify_info['username'];
        $password = $this->verify_info['password'];
        $remember = $this->verify_info['remember'];
        
        if (!$this->CI->auth_model->verify($username, $password)) {
            $this->error = $this->CI->auth_model->getError();
            return false;
        }
        
        $user = $this->CI->auth_model->user;
        $this->add_token_to_db($user['id']);
        $this->save_session($user);
        
        return true;
    }
   
    public function is_authenticated()
    {
        if ($this->is_allowed()) {
            return true;
        }
        if ($this->CI->session->userdata($this->session_name) !== null) {
            return true;
        }
        return false;
    }
    
    /*
     *--------------------------------------------------------------------
     * Kiểm tra controller & action hiện tại có yêu cầu phải đăng nhập hay không
     *
     * @param   void
     * @return  bool
     *--------------------------------------------------------------------
     */
    public function is_allowed(): bool
    {
        $controller = $this->CI->router->fetch_class();
        $method = $this->CI->router->fetch_method();
        if (!isset($this->allowed[$controller])) {
            return false;
        }
        if (!in_array($method, $this->allowed[$controller])) {
            return false;
        }
        return true;
    }
    
    public function add_token_to_db(int $user_id): string
    {
        $this->token = random_string('sha1');
        $data = [
            'token' => $this->token,
            'expire' => $this->verify_info['remember'] === false? 0 : 31536000, // 365 ngày
            'user_id' => $user_id,
            'created_on' => date('Y-m-d H:i:s'),
        ];
        $this->CI->db->insert($this->remember_table, $data);
        return $this->token;
    }
    
    /*
     *--------------------------------------------------------------------
     * Lưu dữ liệu của user đã login vào session
     *
     * @param   array: dữ liệu muốn lưu
     * @return  bool
     *--------------------------------------------------------------------
     */
    protected function save_session(array $user)
    {   
        $data = [
            'token' => $this->token,
            'id' => null,
            'username' => null,
            'fullname' => null,
        ];
        $data = array_update($data, $user);
        $this->CI->session->set_userdata($this->session_name, $data);
    }
}

/* End of file Auth.php */
/* Location: ./application/libraries/Auth.php */