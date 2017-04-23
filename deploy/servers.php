<?php
namespace Deployer;

server('pro_1', 'nvb-online.com')
    ->stage('production')
    ->user('ec2-user')
    ->forwardAgent()
    ->set('deploy_path', '/var/www/html/_kakeibou')
    ->set('http_user', 'webuser')
    ->set('phinx', ['environment' => 'production'])
    ->set('phinx_path', null);
