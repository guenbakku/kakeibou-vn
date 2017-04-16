<?php
namespace Deployer;

server('production', 'nvb-online.com')
    ->stage('pro')
    ->user('ec2-user')
    ->forwardAgent()
    ->set('deploy_path', '/var/www/html/_kakeibou')
    ->set('deploy_user', 'ec2-user')
    ->set('http_user', 'webuser')
    ->set('phinx', ['environment' => 'production'])
    ->set('phinx_path', null);
