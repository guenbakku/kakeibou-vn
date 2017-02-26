<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Trả về url trỏ đến thư mục template đang được sử dụng
 * Yêu cầu phải load sẵn library template
 *
 * @param   void
 * @return  string
 */
function template_url() {
    $CI =& get_instance();
    return $CI->template->template_url();
}

/* End of file template_helper.php */
/* Location: ./application/helpers/template_helper.php */