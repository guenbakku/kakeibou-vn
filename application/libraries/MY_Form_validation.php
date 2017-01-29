<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
    
    /*
     *--------------------------------------------------------------------
     * Reset giá trị của key trong property $_field_data.
     * Sử dụng khi muốn show lại form đã qua validate đồng thời reset lại
     * field trong form đó.
     *
     * @param   mixed: danh sách key muốn reset. 
     *                 Nếu null sẽ reset lại toàn bộ field.
     * @return  object: class object.
     *--------------------------------------------------------------------
     */
    public function reset_field_data($keys = null) {
        if ($keys === null) {
            foreach($this->_field_data as &$key){
                $key['postdata'] = null;
            }
        } else {
            if (!is_array($keys)) {
                $keys = array($keys);
            }
            foreach ($keys as $key) {
                if (isset($this->_field_data[$key])) {
                    $this->_field_data[$key]['postdata'] = null;
                }
            }
        }
        
        return $this;
    }
}
