<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Flash
 * Tạo và hiện thông báo (success or error) dựa vào Flash của Bootstrap.
 */
class Flash
{
    public const SESSION_NAME = 'flash';

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->library('session');
    }

    /**
     * Set Success Message.
     *
     * @param   string
     * @param mixed $msg
     */
    public function success($msg)
    {
        $this->set_session($msg, 'success');
    }

    /**
     * Set Error Message.
     *
     * @param   string
     * @param mixed $msg
     */
    public function error($msg)
    {
        $this->set_session($msg, 'danger');
    }

    /**
     * Printout Bootstrap Flash.
     */
    public function output()
    {
        $data = $this->CI->session->userdata(self::SESSION_NAME);
        unset($_SESSION[self::SESSION_NAME]);

        if ($data === null) {
            return null;
        }

        $html = sprintf('<div class="alert alert-%s fade in">', $data['status']);
        $html .= '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
        $html .= $data['msg'];
        $html .= '</div>';

        return $html;
    }

    /**
     * Save flash data to Session.
     *
     * @param mixed $msg
     * @param mixed $status
     */
    protected function set_session($msg, $status)
    {
        $this->CI->session->set_userdata(
            self::SESSION_NAME,
            [
                'status' => $status,
                'msg' => $msg,
            ]
        );
    }
}
