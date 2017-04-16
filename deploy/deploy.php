<?php
namespace Deployer;
require_once 'recipe/common.php';
require_once '../vendor/deployer/recipes/phinx.php';
require_once 'servers.php';

// Repository
set('repository', 'ssh://gituser@nvb-online.com/opt/git/bhcashbook.git');
set('branch', 'RC3');

// CodeIgniter shared dirs
set('shared_dirs', [
    'application/cache',
    'application/logs',
    'application/session',
    'asset']);

// CodeIgniter writable dirs
set('writable_dirs', [
    'application/cache',
    'application/logs',
    'application/session']);

// Delete unnecessary dirs
set('clear_paths', [
    '.git',
    '_design',
    'deploy']);

// Number of releases to keep
set('keep_releases', 3);
    
// Additional tasks
desc('Set permission of deploy_path to deploy user');
task('chown:before', function(){
    run('sudo chown {{deploy_user}}: -R {{deploy_path}}');
});
desc('Set permission of deploy_path to http user');
task('chown:after', function(){
    run('sudo chown {{http_user}}: -R {{deploy_path}}');
});

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
    'deploy:unlock',
    'cleanup',
]);

before('deploy', 'chown:before');
after('deploy', 'chown:after');
after('deploy', 'success');
after('cleanup', 'phinx:migrate');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
