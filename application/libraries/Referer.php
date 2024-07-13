<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Referer
 * Quản lý HTTP Referer.
 * Dữ liệu này chủ yếu sử dụng trong quá trình tạo link redirect.
 *
 * Các method chính:
 *      get($default) -> lấy link referer.
 *                       Nếu link referer trống hoặc không thuộc domain của app
 *                       thì trả về default
 *
 *      saveSession   -> lưu link referer vào session
 *
 *      getSession    -> lấy link referer từ session
 *                       Cách xử lý dữ liệu trả về giống như method get
 *                       Hàm này khi được gọi sẽ reset lại dữ liệu về link referer trong Session
 */
class Referer
{
    public const SESSION_NAME = 'http_referer';

    protected $http_referer;

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('session');
    }

    /**
     * Lấy http_referer.
     *
     * @param null|mixed $default
     */
    public function get($default = null)
    {
        $this->http_referer = $this->set_http_referer();

        return $this->send_back($default);
    }

    /**
     * Lấy http_referer từ Session.
     *
     * @param null|mixed $default
     * @param mixed      $reset
     */
    public function getSession($default = null, $reset = true)
    {
        $this->http_referer = $this->CI->session->userdata(self::SESSION_NAME);
        if ($reset === true) {
            $this->emptySession();
        }

        return $this->send_back($default);
    }

    /**
     * Lưu http_referer vô Session.
     *
     * @param mixed $rewrite
     */
    public function saveSession($rewrite = true)
    {
        // Không ghi đè session nếu dữ liệu session đã tồn tại
        if ($rewrite == false && !empty($this->CI->session->userdata(self::SESSION_NAME))) {
            return;
        }

        $this->CI->session->set_userdata(
            self::SESSION_NAME,
            $this->set_http_referer()
        );
    }

    /**
     * Reset lại session.
     */
    public function emptySession()
    {
        unset($_SESSION[self::SESSION_NAME]);
    }

    /**
     * Xử lý nên trả về dữ liệu nào
     * Nếu link referer trống hoặc không thuộc domain của app
     *  thì trả về default.
     * Nếu default không quy định thì trả về base_url().
     *
     * @param mixed $default
     */
    protected function send_back($default)
    {
        if (!empty($this->http_referer)) {
            return $this->http_referer;
        }
        if (is_string($default) && !empty($default)) {
            return $default;
        }

        return base_url();
    }

    /**
     * Set dữ liệu http_referer
     * Nếu không quy định thủ công $http_referer thì sẽ lấy dữ liệu từ biến global.
     *
     * @param null|mixed $http_referer
     */
    protected function set_http_referer($http_referer = null)
    {
        if (empty($http_referer)) {
            @$http_referer = $_SERVER['HTTP_REFERER'];
        }

        if (!empty($http_referer) && strpos($http_referer, base_url()) === 0) {
            $this->http_referer = $http_referer;
        }

        return $this->http_referer;
    }
}
