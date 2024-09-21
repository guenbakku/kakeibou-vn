<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('query_string')) {
    function query_string()
    {
        return empty($_SERVER['QUERY_STRING']) ? '' : '?'.$_SERVER['QUERY_STRING'];
    }
}

if (!function_exists('asset_url')) {
    /**
     * Generates a URL for a specified asset.
     * Additionally, it appends a timestamp to the end of the URL
     * to ensure the browser cache refreshes whenever the asset is updated.
     *
     * @param string $path  the path to the asset within the `asset` folder (relative path)
     * @param array  $query The array containing the data to build query param
     */
    function asset_url(string $path, array $query = []): string
    {
        $path = ltrim(normalize_url_path($path), '/');
        $file_path = normalize_url_path(ASSETPATH.$path);
        $modified_time = filemtime($file_path);

        $query = http_build_query(array_merge(
            $query,
            ['t' => $modified_time],
        ));

        return base_url()."asset/{$path}?{$query}";
    }
}

if (!function_exists('template_asset_url')) {
    /**
     * Generates a URL for a specified template asset.
     * Different with `asset_url`, it handle the asset of current using template.
     * Additionally, it appends a timestamp to the end of the URL
     * to ensure the browser cache refreshes whenever the asset is updated.
     *
     * @param string $path  the path to the asset within the `asset/{template}` folder (relative path)
     * @param array  $query The array containing the data to build query param
     */
    function template_asset_url(string $path, array $query = []): string
    {
        $CI = &get_instance();
        $path = ltrim(normalize_url_path($path), '/');
        $file_path = normalize_url_path(
            ASSETPATH
            .$CI->template->folder
            .$path
        );
        $modified_time = filemtime($file_path);

        $query = http_build_query(array_merge(
            $query,
            ['t' => $modified_time],
        ));
        $url_path = $CI->template->template_url()."{$path}?{$query}";

        return $url_path;
    }
}

if (!function_exists('normalize_url_path')) {
    function normalize_url_path(string $path): string
    {
        return str_replace(
            ['/', '\\'],
            ['/', '/'],
            $path,
        );
    }
}

if (!function_exists('normalize_file_path')) {
    function normalize_file_path(string $path): string
    {
        return str_replace(
            ['/', '\\'],
            [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR],
            $path,
        );
    }
}
