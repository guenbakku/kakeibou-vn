<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Memcached settings
| -------------------------------------------------------------------------
| Your Memcached servers can be specified below.
|
|    @see http://codeigniter.com/user_guide/libraries/caching.html#memcached
|
*/
$config = [
    'default' => [
        'hostname' => '127.0.0.1',
        'port' => '11211',
        'weight' => '1',
    ],
];
