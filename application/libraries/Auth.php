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
    public $session_name = 'bhcb_auth';
    public $user_table = 'users';
    public $remember_table = 'remember';
    
    public $error = '';
    
    // Thời gian ghi nhớ đăng nhập (seconds)
    public $remember_duration = 31536000;
    
    public $login_url = 'user/login';
    
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
    
    // Thông tin dùng để đăng nhập
    protected $auth_info = [
        'username'  => null,
        'password'  => null,
        'remember'  => false,
    ];
    
    // Token của session đăng nhập hiện tại
    protected $token = null;
    
    // Thông tin user vừa đăng nhập
    protected $user = [];
    
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
        
        $this->try_to_remember();
    }
    
    public function set_auth_info(array $auth_info) {
        $this->auth_info = array_update($this->auth_info, $auth_info);
        return $this;
    }
    
    public function authenticate(): bool
    {
        $this->del_expired_token_in_db();
        
        $username = $this->auth_info['username'];
        $password = $this->auth_info['password'];
        $remember = $this->auth_info['remember'];
        
        if (!$this->CI->auth_model->verify($username, $password)) {
            $this->error = $this->CI->auth_model->getError();
            return false;
        }
        
        $this->user = $this->CI->auth_model->user;
        $this->token = $this->add_token_to_db($this->user['id']);
        $this->set_session();
        $this->set_cookie();
        
        return true;
    }
    
    public function logout()
    {
        if ($this->is_authenticated()) {
            $token = $this->CI->session->userdata($this->session_name)['token'];
            $this->CI->db->where('token', $token)->delete($this->remember_table);
            $this->CI->session->sess_destroy();
        }
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy thông tin của user đang đăng nhập từ session
     *
     * @param   mixed: key
     * @return  mixed
     *--------------------------------------------------------------------
     */
    public function user(string $key = null)
    {   
        $user = $this->CI->session->userdata($this->session_name);
        if ($key === null) {
            return $user;
        }
        if (isset($user[$key])) {
            return $user[$key];
        }
        
        return null;
    }

    /*
     *--------------------------------------------------------------------
     * Kiểm tra user đã đăng nhập hay chưa
     *
     * @param   void
     * @return  boolean
     *--------------------------------------------------------------------
     */
    public function is_authenticated()
    {
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
    
    /*
     *--------------------------------------------------------------------
     * Cố gắng khôi phục lại thông tin đăng nhập từ token
     *
     * @param   void
     * @return  void
     *--------------------------------------------------------------------
     */
    public function try_to_remember()
    {
        if ($this->is_authenticated()){
            return null;
        }
        
        $token = $this->CI->input->cookie($this->cookie_name);
        
        if (!$this->verify_token($token)){
            return null;
        }
        
        $this->token = $this->renew_token_in_db($token);
        $this->user = $this->get_user_by_token($this->token);
        $this->set_session();
        $this->set_cookie();        
    }
    
    /*
     *--------------------------------------------------------------------
     * Xóa những token hết hạn trong db
     *
     * @param   void
     * @return  void
     *--------------------------------------------------------------------
     */
    public function del_expired_token_in_db()
    {
        $this->CI->db
                 ->where('expire_on <', date('Y-m-d H:i:s'))
                 ->delete($this->remember_table);
    }
    
    /*
     *--------------------------------------------------------------------
     * Thêm token mới vào đb cho account vừa đăng nhập
     * 
     * @param   int: id của user
     * @return  string: token vừa mới thêm vào db
     *--------------------------------------------------------------------
     */
    public function add_token_to_db(int $user_id): string
    {
        $new_token = $this->gen_token();
        $now = time();
        $data = [
            'token'      => $new_token,
            'user_id'    => $user_id,
            'user_agent' => $this->CI->input->user_agent(),
            'expire_on'  => $this->auth_info['remember'] === false
                            ? date('Y-m-d H:i:s', $now) 
                            : date('Y-m-d H:i:s', $now + $this->remember_duration),
            'created_on' => date('Y-m-d H:i:s', $now),
        ];
        $this->CI->db->insert($this->remember_table, $data);
        return $new_token;
    }
    
    /*
     *--------------------------------------------------------------------
     * Renew token trong db
     * 
     * @param   string: token cũ
     * @return  string: token mới
     *--------------------------------------------------------------------
     */
    public function renew_token_in_db(string $old_token): string
    {
        $new_token = $this->gen_token();
        $this->CI->db
                 ->set('token', $new_token)
                 ->where('token', $old_token)
                 ->update($this->remember_table);
        return $new_token;
    }
     
    
    /*
     *--------------------------------------------------------------------
     * Tạo token và đảm bảo token chưa tồn tại trong db
     *
     * @param   void
     * @return  string
     *--------------------------------------------------------------------
     */
    public function gen_token(): string
    {
        $token_existed = function($token) {
            $query = $this->CI->db
                          ->select('id')
                          ->where('token', $token)
                          ->limit(1)
                          ->get($this->remember_table);
            return $query->num_rows() > 0;
        };
        $token = random_string('sha1');
        while ($token_existed($token)) {
            $token = random_string('sha1');
        }
        return $token;
    }
    
    /*
     *--------------------------------------------------------------------
     * Validate token có còn hiệu lực hay không
     *
     * @param   string: token
     * @return  bool
     *--------------------------------------------------------------------
     */
    public function verify_token(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }
        
        $data = $this->CI->db
                         ->select('expire_on')
                         ->where('token', $token)
                         ->limit(1)
                         ->get($this->remember_table)->row_array();
                         
        if (empty($data)) {
            return false;
        }
        
        $expire_on = strtotime($data['expire_on']);
        if ($expire_on < time()) {
            return false;
        }
        
        return true;
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy token từ cookie
     *
     * @param   void
     * @return  string
     *--------------------------------------------------------------------
     */
    public function get_token_from_cookie(): string
    {
        return $this->CI->input->cookie($this->cookie_name);
    }
    
    /*
     *--------------------------------------------------------------------
     * Lấy thông tin user đăng nhập từ token
     *
     * @param   string: token
     * @return  array
     *--------------------------------------------------------------------
     */
    public function get_user_by_token(string $token): ?array
    {   
        $select = [
            'remember.token', 'users.id', 'users.username', 'users.fullname'
        ];
        $user = $this->CI->db
                         ->select($select)
                         ->where('remember.token', $token)
                         ->from($this->remember_table)
                         ->join($this->user_table, 'users.id = remember.user_id')
                         ->limit(1)
                         ->get()->row_array();
        return $user;
    }
    
    /*
     *--------------------------------------------------------------------
     * Lưu dữ liệu của user đã login vào session
     *
     * @param   array: dữ liệu muốn lưu
     * @return  bool
     *--------------------------------------------------------------------
     */
    protected function set_session()
    {   
        $data = [
            'token'    => $this->token,
            'id'       => null,
            'username' => null,
            'fullname' => null,
        ];
        $data = array_update($data, $this->user);
        $this->CI->session->set_userdata($this->session_name, $data);
    }
    
    /*
     *--------------------------------------------------------------------
     * Set cookie chứa token để gửi trả về cho client
     *
     * @param   void
     * @return  void
     *--------------------------------------------------------------------
     */
    public function set_cookie()
    {
        $this->CI->input->set_cookie(array(
            'name'      => $this->cookie_name,
            'value'     => $this->token,
            'expire'    => $this->remember_duration,
        ));
    }
}

/* End of file Auth.php */
/* Location: ./application/libraries/Auth.php */