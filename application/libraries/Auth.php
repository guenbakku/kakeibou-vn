<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * CodeIgniter Authentication & Authorization Class.
 *
 * This class contains process which handle authentication and authorization.
 * This class need model auth_model for communicating with database.
 *
 * @author        Nguyen Van Bach
 *
 * @category    Libraries
 *
 * @see        http://nvb-online.com
 *
 * @copyright   Copyright (c) 2017, Nguyen Van Bach.
 * @license     MIT
 *
 * @version 0.0.1
 */
class Auth
{
    public $error = '';

    protected $CI;

    protected $settings = [
        'cookie_name' => 'bhcb_token',
        'session_name' => 'bhcb_auth',
        'user_table' => 'users',
        'token_table' => 'tokens',
        'login_url' => 'user/login',

        // Thời gian hiệu lực tối đa của token.
        // Nếu khi đăng nhập có chọn "remember" thì thời gian hiệu lực
        // của token sẽ là giá trị này.
        // Ngược lại token sẽ có hiệu lực trong 1 ngày.
        'token_duration_max' => 31536000,

        // Số lần nhập sai mật khẩu liên tiếp tối đa.
        // Nếu số lần nhập sai mật khẩu liên tiếp vượt quá giá trị này,
        // tài khoản sẽ bị khóa.
        'login_attemps_max' => 5,

        // Thời gian khóa tài khoản tối thiểu.
        // Thời gian này sẽ tăng theo cấp số cộng nếu sau khi hết thời gian khóa
        // vẫn nhập sai mật khẩu.
        'lock_duration_min' => 300,
    ];

    // Danh sách controller và action cho phép truy cập không cần authenticate
    // Format: ['controller/action', ...]
    protected $allowed = [];

    // Thông tin dùng để đăng nhập
    protected $auth_info = [
        'username' => null,
        'password' => null,
        'remember' => false,
    ];

    // Token của session đăng nhập hiện tại
    protected $token;

    // Cache lại giá trị expire_on của token
    protected $cache = [];

    // Thông tin user vừa đăng nhập
    protected $user = [];

    public function __construct()
    {
        // Copy an instance of CI so we can use the entire framework.
        $this->CI = &get_instance();
        $this->CI->load->library('session');
        $this->CI->load->helper('string');
        $this->CI->load->model('app_model');
        $this->CI->load->model('auth_model');

        $this->try_to_remember();
    }

    /**
     * Config cho auth.
     */
    public function config(array $settings)
    {
        $this->settings = array_update($this->settings, $settings);

        return $this;
    }

    /**
     * Set thông tin xác thực.
     * Thông tin này sẽ được check ở method authenticate.
     */
    public function auth_info(array $auth_info)
    {
        $this->auth_info = array_update($this->auth_info, $auth_info);

        return $this;
    }

    /**
     * Destroy tất cả thông tin đăng nhập của session hiện tại.
     * Chủ yếu sử dụng khi muốn logout.
     */
    public function destroy()
    {
        $session_name = $this->settings['session_name'];
        $token = $this->CI->session->userdata($session_name)['token'];
        $this->CI->db->where('token', $token)->delete($this->settings['token_table']);
        $this->CI->session->sess_destroy();
    }

    /**
     * Thực hiện xác thực tài khoản.
     *
     * @return bool xác thực thành công hay không
     */
    public function authenticate(): bool
    {
        $this->del_expired_token_in_db();

        $this->CI->auth_model->config($this->settings);

        $username = $this->auth_info['username'];
        $password = $this->auth_info['password'];

        if (!$this->CI->auth_model->verify($username, $password)) {
            $this->error = $this->CI->auth_model->get_error();

            return false;
        }

        $this->user = $this->get_user_from_db(['users.username' => $this->auth_info['username']]);
        $this->token = $this->add_token_to_db($this->user['id']);
        $this->set_session();
        $this->set_cookie();

        return true;
    }

    /**
     * Cố gắng khôi phục lại thông tin đăng nhập từ token.
     */
    public function try_to_remember()
    {
        if ($this->is_authenticated() === true) {
            return null;
        }

        $token = $this->CI->input->cookie($this->settings['cookie_name']);
        if ($this->is_valid_token($token) === false) {
            return null;
        }

        $this->user = $this->get_user_from_db(['tokens.token' => $token]);
        $this->token = $this->renew_token_in_db($token);
        $this->set_session();
        $this->set_cookie();
    }

    /**
     * Lấy thông tin của user đang đăng nhập từ session.
     *
     * @param null|string $key tên field muốn lấy. Nếu null sẽ trả về toàn bộ data trong session
     */
    public function user(?string $key = null)
    {
        $session_name = $this->settings['session_name'];
        $user = $this->CI->session->userdata($session_name);
        if ($key === null) {
            return $user;
        }
        if (isset($user[$key])) {
            return $user[$key];
        }

        return null;
    }

    /**
     * Kiểm tra user đã được chứng thực hay chưa (đã đăng nhập hay chưa).
     *
     * @return bool
     */
    public function is_authenticated()
    {
        $session_name = $this->settings['session_name'];
        if ($this->CI->session->userdata($session_name) == null) {
            return false;
        }

        $token = $this->user('token');
        if ($this->is_valid_token($token) === false) {
            return false;
        }

        return true;
    }

    /**
     * Kiểm tra controller & action hiện tại có bắt buộc phải đăng nhập
     * mới access được hay không.
     */
    public function is_allowed(): bool
    {
        $identifier = implode('/', [
            $this->CI->router->fetch_class(),
            $this->CI->router->fetch_method(),
        ]);
        $this->allow($this->settings['login_url']);

        return in_array($identifier, $this->allowed);
    }

    /**
     * Thêm identifier vào danh sách allowed url.
     *
     * @param string|string[] $identifiers
     */
    public function allow($identifiers)
    {
        if (!is_array($identifiers)) {
            $identifiers = [$identifiers];
        }

        foreach ($identifiers as $identifier) {
            if (!is_string($identifier)) {
                throw new InvalidArgumentException('Allowed identifier must be string');
            }
            $identifier = trim($identifier, '/');
            $identifier = strtolower($identifier);
            $this->allowed[] = $identifier;
        }
    }

    /**
     * Trả về login url trong settings.
     */
    public function login_url(): string
    {
        return $this->settings['login_url'];
    }

    /**
     * Update lại thông tin user lưu trong session hiện tại.
     * Sử dụng user_id trong session để lấy thông tin user mới từ db.
     */
    public function update_session()
    {
        if ($this->is_authenticated()) {
            $this->user = $this->get_user_from_db(['users.id' => $this->user('id')]);
            $this->token = $this->user('token');
            $this->set_session();
        }
    }

    /**
     * Xóa tất cả token của user hiện có trong db trừ token của session hiện tại.
     * Chủ yếu sử dụng khi thay đổi mật khẩu đăng nhập.
     */
    public function delete_all_other_tokens_of_user(?int $user_id = null)
    {
        if ($this->is_authenticated()) {
            if ($user_id === null) {
                $user_id = $this->user('id');
            }
            $current_token = $this->user('token');
            $this->CI->db->where('token !=', $current_token)
                ->where('user_id', $user_id)
                ->delete($this->settings['token_table'])
            ;
        }
    }

    /**
     * Lấy thông tin user đăng nhập từ database.
     *
     * @param array $where array chứa điều kiện where
     */
    protected function get_user_from_db(array $where): ?array
    {
        $select = [
            'users.id', 'users.username', 'users.fullname',
        ];

        return $this->CI->db
            ->select($select)
            ->where($where)
            ->from($this->settings['user_table'])
            ->join($this->settings['token_table'], 'users.id = tokens.user_id', 'left')
            ->limit(1)
            ->get()->row_array()
        ;
    }

    /**
     * Trả về thời gian hiệu lục của token.
     */
    protected function token_duration(): int
    {
        if ($this->auth_info['remember'] === true) {
            return $this->settings['token_duration_max'];
        }

        return 86400; // 1 ngày
    }

    /**
     * Xóa những token hết hạn trong db.
     */
    protected function del_expired_token_in_db()
    {
        $this->CI->db
            ->where('expire_on <', date('Y-m-d H:i:s'))
            ->delete($this->settings['token_table'])
        ;
    }

    /**
     * Thêm token mới vào đb cho account vừa đăng nhập.
     *
     * @param int $user_id id của user
     *
     * @return string token vừa mới thêm vào db
     */
    protected function add_token_to_db(int $user_id): string
    {
        $new_token = $this->gen_token();
        $now = time();
        $data = [
            'token' => $new_token,
            'user_id' => $user_id,
            'user_agent' => $this->CI->input->user_agent(),
            'expire_on' => date('Y-m-d H:i:s', $now + $this->token_duration()),
            'created_on' => date('Y-m-d H:i:s', $now),
            'modified_on' => date('Y-m-d H:i:s', $now),
        ];
        $this->CI->db->insert($this->settings['token_table'], $data);

        return $new_token;
    }

    /**
     * Renew token trong db.
     *
     * @param string $old_token token cũ
     *
     * @return string token mới
     */
    protected function renew_token_in_db(string $old_token): string
    {
        $new_token = $this->gen_token();
        $this->CI->db
            ->set('token', $new_token)
            ->set('user_agent', $this->CI->input->user_agent())
            ->set('modified_on', date('Y-m-d H:i:s'))
            ->where('token', $old_token)
            ->update($this->settings['token_table'])
        ;

        return $new_token;
    }

    /**
     * Tạo token và đảm bảo token chưa tồn tại trong db.
     */
    protected function gen_token(): string
    {
        $token_existed = function ($token) {
            $query = $this->CI->db
                ->select('token')
                ->where('token', $token)
                ->limit(1)
                ->get($this->settings['token_table'])
            ;

            return $query->num_rows() > 0;
        };
        $token = random_string('sha1');
        while ($token_existed($token)) {
            $token = random_string('sha1');
        }

        return $token;
    }

    /**
     * Validate token có còn hiệu lực hay không.
     */
    protected function is_valid_token(?string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        if (isset($this->cache[$token])) {
            $expire_on = $this->cache[$token];
        } else {
            $data = $this->CI->db
                ->select('expire_on')
                ->where('token', $token)
                ->limit(1)
                ->get($this->settings['token_table'])
                ->row_array()
            ;

            if (empty($data)) {
                return false;
            }

            $expire_on = $data['expire_on'];
            $this->cache[$token] = $expire_on;
        }

        return strtotime($expire_on) >= time();
    }

    /**
     * Lưu dữ liệu của user đã login vào session.
     */
    protected function set_session()
    {
        $data = [
            'token' => $this->token,
            'id' => null,
            'username' => null,
            'fullname' => null,
        ];
        $data = array_update($data, $this->user);
        $session_name = $this->settings['session_name'];
        $this->CI->session->set_userdata($session_name, $data);
    }

    /**
     * Set cookie chứa token để gửi trả về cho client.
     */
    protected function set_cookie()
    {
        $this->CI->input->set_cookie([
            'name' => $this->settings['cookie_name'],
            'value' => $this->token,
            'expire' => $this->settings['token_duration_max'],
            'secure' => config_item('cookie_secure'),
        ]);
    }
}

// End of file Auth.php
// Location: ./application/libraries/Auth.php
