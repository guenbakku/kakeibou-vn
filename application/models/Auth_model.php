<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends App_Model
{
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

    public function get_table(): string
    {
        return 'users';
    }

    /**
     * Kiểm tra đăng nhập của user.
     */
    public function verify(string $username, string $password): bool
    {
        try {
            if (empty($username) || strlen($username) > 32) {
                throw new AppException(settings('err_login_info_invalid'));
            }

            $this->user = $this->db->select('id, username, password, fullname, locked_on, lock_duration, login_attemps')
                ->from($this->get_table())
                ->where('username', $username)
                ->limit(1)
                ->get()->row_array()
            ;

            // Tài khoản không tồn tại
            if (empty($this->user)) {
                throw new AppException(settings('err_login_info_invalid'));
            }

            // Tài khoản bị khóa
            if ($this->user['lock_duration'] > 0) {
                $locked_to = strtotime($this->user['locked_on']) + $this->user['lock_duration'];
                $now = time();
                if ($locked_to > $now) {
                    throw new AppException(sprintf(settings('err_user_locked'), (int) (($locked_to - $now) / 60)));
                }
            }

            // Password không match
            if (!password_verify($password, $this->user['password'])) {
                $this->lock_account();

                throw new AppException(settings('err_login_info_invalid'));
            }

            $this->reset_locked_account();

            return true;
        } catch (AppException $e) {
            $this->set_error($e->getMessage());

            return false;
        }
    }

    /**
     * Xử lý khóa tài khoản nếu password bị sai.
     */
    private function lock_account()
    {
        $user = [];
        $user['login_attemps'] = $this->user['login_attemps'] + 1;
        if ($user['login_attemps'] % $this->settings['login_attemps_max'] === 0) {
            $user['locked_on'] = date('Y-m-d H:i:s');
            $user['lock_duration'] = $this->user['lock_duration'] > 0
                                     ? $this->user['lock_duration'] * 2
                                     : $this->settings['lock_duration_min'];
        }
        $this->db->where('id', $this->user['id'])
            ->update($this->get_table(), $user)
        ;
    }

    /**
     * Reset lại thông tin khóa tài khoản nếu đăng nhập thành công.
     */
    private function reset_locked_account()
    {
        $user = [
            'login_attemps' => 0,
            'locked_on' => null,
            'lock_duration' => 0,
        ];

        $this->db->where('id', $this->user['id'])
            ->update($this->get_table(), $user)
        ;
    }
}
