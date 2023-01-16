<?php
// 创建资源控制器
ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once __DIR__ . '/server.php';

if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
    $server->getResponse()->send();
    die;
}
echo json_encode([
    'success' => true,
    'message' => '你访问了我的API！！！'
]);

// --执行
// curl http://localhost/resource.php -d 'access_token=YOUR_TOKEN'

// PHP-cURL
// POST
/*$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://auth2.test/resource.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'access_token=9a34984e327a0d3d1938efce6a604e766ab69687',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;*/
