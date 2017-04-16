<?php
$_ENV['PHINX_DDL_DIR_PATH'] = __DIR__ . '/db/ddl';
$_ENV['PHINX_DB_CONFIG_PATH'] = [
    'production' => __DIR__ . '/application/config/production/database.php',
    'development' => __DIR__ . '/application/config/database.php',
];

/*
 * Required when include Codeigniter file invidually.
 */
define('BASEPATH', true); 
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

/*
 * Get config of data from application config file's
 */
function get_db_config($env) {
    $path = $_ENV['PHINX_DB_CONFIG_PATH'][$env];
    require ($path);
    return $db['default'];
}

$db['production'] = get_db_config('production');
$db['development'] = get_db_config('development');

return [
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/db/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/db/seeds',
    ],
    
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_database' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => $db['production']['hostname'],
            'name' => $db['production']['database'],
            'user' => 'root',
            'pass' => 'king=miu',
            'port' => 3306,
            'charset' => $db['production']['char_set'],
        ],
    
        'development' => [
            'adapter' => 'mysql',
            'host' => $db['development']['hostname'],
            'name' => $db['development']['database'],
            'user' => $db['development']['username'],
            'pass' => $db['development']['password'],
            'port' => 3306,
            'charset' => $db['development']['char_set'],
        ],
    ],
    
    'version_order' => 'creation',
];