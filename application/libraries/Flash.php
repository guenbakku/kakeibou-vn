<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Flash
 * Tạo và hiện thông báo (success or error) dựa vào Flash của Bootstrap
 *
 */

class Flash {
    
    const SESSION_NAME = 'flash';
    
    private $CI;
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('session');
    }
    
    /*
     *--------------------------------------------------------------------
     * Set Success Message
     *
     *--------------------------------------------------------------------
     */
    public function success($msg)
    {
        $this->set_session('success', $msg);
    }
    
    /*
     *--------------------------------------------------------------------
     * Set Error Message
     *
     *--------------------------------------------------------------------
     */
    public function error($msg)
    {
        $this->set_session('danger', $msg);
    }
    
    /*
     *--------------------------------------------------------------------
     * Printout Bootstrap Flash
     *
     *--------------------------------------------------------------------
     */
    public function output()
    {
        if (null === $data = $this->CI->session->userdata(self::SESSION_NAME)){
            return null;
        }
        
        $html  = sprintf('<div class="alert alert-%s fade in">', $data['status']);
        $html .= '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
        $html .= $data['msg'];
        $html .= '</div>';
        
        unset($_SESSION[self::SESSION_NAME]);
        
        return $html;
    }
    
    /*
     *--------------------------------------------------------------------
     * Save flash data to Session
     *
     *--------------------------------------------------------------------
     */
    private function set_session($status, $msg)
    {
        $this->CI->session->set_userdata(
            self::SESSION_NAME, 
            array(
                'status' => $status,
                'msg'    => $msg
            )
        );
    }
}