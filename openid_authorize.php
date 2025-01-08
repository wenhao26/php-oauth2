<?php
// 使用OpenID
use OAuth2\Request;
use OAuth2\Response;

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'openid.php';

// 该页面请求地址类似：
// http://auth2.test/openid_authorize.php?response_type=code&client_id=testclient&state=xyz&redirect_uri=http://auth2.test/openid_cb.php?scope=basic%20get_user_info%20upload_pic%20openid

//echo urlencode(urldecode('http://auth2.test/openid_cb.php?scope=basic%20get_user_info%20upload_pic%20openid'));die;

$request  = Request::createFromGlobals();
$response = new Response();

// 验证 authorize request
// 这里会验证client_id，redirect_uri等参数和client是否有scope
if (!$server->validateAuthorizeRequest($request, $response)) {
    $response->send();
    die;
}

// 显示授权登录页面
if (empty($_POST)) {
    /*// 获取client类型的storage
    // 不过这里我们在server里设置了storage，其实都是一样的storage->pdo.mysql
    $pdo = $server->getStorage('client');

    // 获取oauth_clients表的对应的client应用的数据
    $clientInfo = $pdo->getClientDetails($request->query('client_id'));
    $this->assign('clientInfo', $clientInfo);
    $this->display('authorize');
    die();*/

    exit('<form method="post">
  <label>Do You Authorize TestClient?</label><br />
  <input type="submit" name="authorized" value="yes">
  <input type="submit" name="authorized" value="no">
</form>');
}

$isAuthorized = ($_POST['authorized'] === 'yes');

// 这里是授权获取code，并拼接Location地址返回相应
// Location的地址类似：http://sxx.qkl.local/v2/oauth/cb?code=69d78ea06b5ee41acbb9dfb90500823c8ac0241d&state=xyz
$userId = 1234;
$server->handleAuthorizeRequest($request, $response, $isAuthorized, $userId);
if ($isAuthorized) {
    // 这里会创建Location跳转，你可以直接获取相关的跳转url，用于debug
    $parts = parse_url($response->getHttpHeader('Location'));
    var_dump($parts);die;
    parse_str($parts['query'], $query);

    // 拉取oauth_authorization_codes记录的信息，包含id_token
    $code = $server->getStorage('authorization_code')->getAuthorizationCode($query['code']);
    var_dump($code);
}
$response->send();
