<?php
// 创建令牌控制器
ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once __DIR__ . '/server.php';

$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();


// --执行
// curl -u testclient:testpass http://localhost/token.php -d 'grant_type=client_credentials'

// PHP-cURL
// POST
/*$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL            => 'http://auth2.test/token.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING       => '',
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_TIMEOUT        => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => 'POST',
    CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
    CURLOPT_HTTPHEADER     => array(
        'Authorization: Basic dGVzdGNsaWVudDp0ZXN0cGFzcw==', // dGVzdGNsaWVudDp0ZXN0cGFzcw== 这样生成： base64_encode('testclient:testpass')
        'Content-Type: application/x-www-form-urlencoded'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;*/