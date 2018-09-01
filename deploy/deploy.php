<?php
namespace Deployer;
require_once 'recipe/common.php';
require_once '../vendor/deployer/recipes/phinx.php';
require_once 'config.php';




// Main task
desc('Deploy app');
task('deploy', [
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:vendors',
    'deploy:shared',
    'deploy:symlink',
    'deploy:writable',
    'deploy:unlock',
    'cleanup',
]);

after('deploy', 'success');
after('cleanup', 'phinx:migrate');
after('phinx:migrate', 'deploy:clear_paths');
after('deploy:failed', 'deploy:unlock');
