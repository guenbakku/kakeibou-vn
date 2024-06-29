<?php
php_sapi_name() === 'cli' or die('No direct script access allowed');

// Load dotenv file
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$PHINX_DIRNAME = 'db';

$_ENV['PHINX_DDL_DIR_PATH'] = __DIR__ . "/$PHINX_DIRNAME/ddl";

/*
 * Required when include Codeigniter file invidually.
 */
define('BASEPATH', true);
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

/*
 * Get config of data from application config file's
 */
function get_db_config() {
    require (__DIR__ . '/application/config/database.php');
    return $db['default'];
}

$db = get_db_config();

return [
    'paths' => [
        'migrations' => "%%PHINX_CONFIG_DIR%%/$PHINX_DIRNAME/migrations",
        'seeds' => "%%PHINX_CONFIG_DIR%%/$PHINX_DIRNAME/seeds",
    ],

    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => $db['hostname'],
            'name' => $db['database'],
            'user' => $db['username'],
            'pass' => $db['password'],
            'port' => 3306,
            'charset' => $db['char_set'],
        ],
    ],

    'version_order' => 'creation',
];
