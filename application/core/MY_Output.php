<?php

defined('BASEPATH') or exit('No direct script access allowed');

class MY_Output extends CI_Output
{
    /**
     * Xóa những header không cần thiết ra khỏi response của PHP.
     *
     * @param string|string[] $headers tên những header muốn xóa
     */
    public function remove_headers(array|string $headers = [])
    {
        if (!is_array($headers)) {
            $headers = [$headers];
        }

        header_register_callback(function () use ($headers) {
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
