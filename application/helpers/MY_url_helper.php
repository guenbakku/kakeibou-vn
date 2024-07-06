<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function query_string()
{
    return empty($_SERVER['QUERY_STRING']) ? '' : '?'.$_SERVER['QUERY_STRING'];
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

/**
 * Generates a URL for a specified asset.
 * Additionally, it appends a timestamp to the end of the URL
 * to ensure the browser cache refreshes whenever the asset is updated.
 *
 * @param string $path The path to the asset within the `asset` folder (relative path).
 * @param array $query The array containing the data to build query param
 * @return string
 */
function asset_url(string $path, array $query = []): string {
    $path = ltrim(normalize_url_path($path), '/');
    $file_path = normalize_url_path(ASSETPATH . $path);
    $modified_time = filemtime($file_path);

    $query = http_build_query(array_merge(
        $query,
        ['t' => $modified_time],
    ));
    $url_path = base_url()."asset/$path?$query";
    return $url_path;
}

function normalize_url_path(string $path): string {
    return str_replace(
        ['/', '\\'],
        ['/', '/'],
        $path,
    );
}

function normalize_file_path(string $path): string {
    return str_replace(
        ['/', '\\'],
        [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR],
        $path,
    );
}
