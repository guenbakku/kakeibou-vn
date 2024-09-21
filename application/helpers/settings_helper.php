<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('settings')) {
    function settings(string $path, ?string $default = null)
    {
        $ci = &get_instance();
        $ci->load->library('settings');

        return $ci->settings->get($path, $default);
    }
}
