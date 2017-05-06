<?php
namespace Deployer;
require_once 'recipe/common.php';
require_once '../vendor/deployer/recipes/phinx.php';
require_once 'servers.php';

// Repository
set('repository', 'ssh://gituser@nvb-online.com/opt/git/bhcashbook.git');
set('branch', 'master');

// CodeIgniter shared dirs
set('shared_dirs', [
    'application/cache',
    'application/logs',
    'application/session']);

// CodeIgniter writable dirs
set('writable_dirs', [
    'application/cache',
    'application/logs',
    'application/session']);
    
set('writable_mode', 'chown');
set('writable_use_sudo', true);

// Delete unnecessary dirs
set('clear_paths', [
    '.git',
    '_design',
    'deploy']);

// Number of releases to keep
set('keep_releases', 3);


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
    'deploy:clear_paths',
    'deploy:writable',
    'deploy:unlock',
    'cleanup',
]);

after('deploy', 'success');
after('cleanup', 'phinx:migrate');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
