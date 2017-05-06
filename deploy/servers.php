<?php
namespace Deployer;

set('ssh_type', 'native');
set('ssh_multiplexing', true);

server('pro_1', 'nvb-online.com')
    ->stage('production')
    ->user('ec2-user')
    ->pemFile('~/.ssh/auto_load_private_keys/ec2-virginia-ec2-user.pem')
    ->set('deploy_path', '/var/www/html/_kakeibou')
    ->set('http_user', 'webuser')
    ->set('phinx', ['environment' => 'production'])
    ->set('phinx_path', null);
