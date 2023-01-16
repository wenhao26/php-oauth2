<?php

use OAuth2\Autoloader;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\GrantType\UserCredentials;
use OAuth2\Server;

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'oauth2-server-php/src/OAuth2/Autoloader.php';
Autoloader::register();

$storage = new \OAuth2\Storage\Pdo([
    'dsn'      => 'mysql:dbname=auth2;host=127.0.0.1',
    'username' => 'root',
    'password' => 'root'
]);
$server  = new Server($storage);
$server->addGrantType(new AuthorizationCode($storage));
$server->addGrantType(new ClientCredentials($storage));
$server->addGrantType(new UserCredentials($storage));
