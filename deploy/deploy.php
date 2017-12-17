<?php
namespace Deployer;
require_once 'recipe/common.php';
require_once '../vendor/deployer/recipes/phinx.php';
require_once 'servers.php';

// Repository
set('repository', 'ssh://git@redmine.nvb-online.com/kakeibou/gl-bhcashbook.git');
set('branch', 'master');

// Codeigniter shared dirs
set('shared_dirs', [
    'application/cache',
    'application/logs',
    'application/session']);

// Codeigniter writable dirs
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
    'deploy',
    'phinx',
    'phinx.php']);

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
    'deploy:writable',
    'deploy:unlock',
    'cleanup',
]);

after('deploy', 'success');
after('cleanup', 'phinx:migrate');
after('phinx:migrate', 'deploy:clear_paths');
after('deploy:failed', 'deploy:unlock');
