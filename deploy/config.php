<?php
namespace Deployer;

set('ssh_type', 'native');
set('ssh_multiplexing', true);

server('pro_1', 'nvb-online.com')
    ->stage('production')
    ->user('ec2-user')
    ->pemFile('~/.ssh/private_keys/ec2-virginia-ec2-user.pem')
    ->set('deploy_path', '/var/www/html/_kakeibou')
    ->set('http_user', 'webuser')
    ->set('phinx', ['environment' => 'production'])
    ->set('phinx_path', null);

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