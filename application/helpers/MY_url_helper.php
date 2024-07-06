<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function my_site_url()
{
    $url = strtolower(site_url(func_get_args()));
    return str_replace('/index.php', '', $url);
}

function query_string()
{
    return empty($_SERVER['QUERY_STRING'])? '' : '?'.$_SERVER['QUERY_STRING'];
}

/**
 * Trả về url trỏ đến thư mục template đang được sử dụng.
 * Yêu cầu phải load sẵn library template
 *
 * @param   void
 * @return  string
 */
function template_url() {
    $CI =& get_instance();
    return $CI->template->template_url();
}
