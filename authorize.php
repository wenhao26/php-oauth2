<?php
// 创建授权控制器
use OAuth2\Request;
use OAuth2\Response;

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once __DIR__ . '/server.php';

// 该文件是用户确认的交互UI
// 用户点击同意会获取 authorization_code，然后再获取 access_token
// 在浏览器打开如下链接，然后点击yes按钮
// http://{站点域名}/authorize.php?response_type=code&client_id=testclient&state=xyz

// 授权控制器是 OAuth2 的“杀手级功能”，允许您的用户授权第三方应用程序。
// 与第一个令牌控制器示例中发生的那样直接颁发访问令牌不同，在此示例中，授权控制器用于仅在用户授权请求后才颁发令牌

$request  = Request::createFromGlobals();
$response = new Response();

// 验证授权请求
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}

// 显示授权表单
if (empty($_POST)) {
    exit('<form method="post">
  <label>Do You Authorize TestClient?</label><br />
  <input type="submit" name="authorized" value="yes">
  <input type="submit" name="authorized" value="no">
</form>');
}

$isAuthorized = ($_POST['authorized'] === 'yes');
// $server->handleAuthorizeRequest($request, $response, $isAuthorized);

// 将本地用户与访问令牌相关联
$userId = 1234;
$server->handleAuthorizeRequest($request, $response, $isAuthorized, $userId);
if ($isAuthorized) {
    $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);
    exit("成功！授权代码:{$code}"); // 授权码将在 30 秒后过期
}
$response->send();


// --执行
// http://localhost/authorize.php?response_type=code&client_id=testclient&state=xyz

// PHP-cURL
// POST
/*$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'http://auth2.test/token.php',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => 'code=7375fe4f6c03d0dc5bbebaa895206d43f8b0f674&grant_type=authorization_code',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Basic dGVzdGNsaWVudDp0ZXN0cGFzcw==',
        'Content-Type: application/x-www-form-urlencoded'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;*/