<?php
// 使用OpenID
use OAuth2\Autoloader;
use OAuth2\GrantType\AuthorizationCode;
use OAuth2\GrantType\ClientCredentials;
use OAuth2\Request;
use OAuth2\Response;
use OAuth2\Server;
use OAuth2\Storage\Memory;

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'oauth2-server-php/src/OAuth2/Autoloader.php';
Autoloader::register();

$storage = new \OAuth2\Storage\Pdo([
    'dsn'      => 'mysql:dbname=auth2;host=127.0.0.1',
    'username' => 'root',
    'password' => 'root'
]);

// 设置 use_openid_connect 和 issuer 配置参数
$config = [
    'use_openid_connect' => true,
    'issuer'             => 'auth2.test'
];
$server = new Server($storage, $config);

// 创建秘钥存储并将其添加到服务器
$publicKey  = file_get_contents('pem/public_key.pem');
$privateKey = file_get_contents('pem/private_key.pem');

$keyStorage = new Memory([
    'keys' => [
        'public_key'  => $publicKey,
        'private_key' => $privateKey,
    ]
]);
$server->addStorage($keyStorage, 'public_key');

// 添加 Authorization Code 授予类型
$server->addGrantType(new AuthorizationCode($storage));

// 添加 Client Credentials 授予类型
// 一般三方应用都是直接通过client_id & client_secret直接请求获取access_token
$server->addGrantType(new ClientCredentials($storage));

/*// 验证OpenID链接
$request = new Request([
    'client_id'     => 'testclient',
    'redirect_uri'  => 'http://auth2.test',
    'response_type' => 'code',
    'scope'         => 'openid',
    'state'         => 'xyz',
]);
$response = new Response();
$server->handleAuthorizeRequest($request, $response, true);

$parts = parse_url($response->getHttpHeader('Location'));var_dump($parts);die;
parse_str($parts['query'], $query);

$code = $server->getStorage('authorization_code')->getAuthorizationCode($query['code']);

echo '<pre>';
var_dump($code);*/