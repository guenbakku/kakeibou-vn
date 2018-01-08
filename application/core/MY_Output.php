<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Output extends CI_Output {
    
    /**
     * Xóa những header không cần thiết
     * 
     * @param   string/array: tên những header muốn xóa
     * @return  void
     */
    public function remove_headers($headers = array())
    {
        if (!is_array($headers)) {
            $headers = array($headers);
        }
        
        header_register_callback(function() use ($headers){
            if (function_exists('header_remove')) {
                foreach ($headers as $header) {
                    if (!is_string($header) || empty($header)) {
                        continue;
                    }
                    header_remove($header);
                }
            }
        });
    }
}
