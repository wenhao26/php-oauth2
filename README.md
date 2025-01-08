[OAuth 2.0 Server PHP]
===============

> 相关文档 <br>
* https://bshaffer.github.io/oauth2-server-php-docs/cookbook/
* https://bshaffer.github.io/oauth2-server-php-docs/overview/openid-connect/


## 部署说明

* 下载包 https://github.com/bshaffer/oauth2-server-php
* 创建数据库
* 创建主引导文件 server.php
* 创建令牌控制器 token.php
* 创建资源控制器 resource.php
* 创建授权控制器 authorize.php


#### 1、创建默认数据库，将一下表结构进行初始化
~~~
CREATE TABLE oauth_clients (
  client_id             VARCHAR(80)   NOT NULL,
  client_secret         VARCHAR(80),
  redirect_uri          VARCHAR(2000),
  grant_types           VARCHAR(80),
  scope                 VARCHAR(4000),
  user_id               VARCHAR(80),
  PRIMARY KEY (client_id)
);

CREATE TABLE oauth_access_tokens (
  access_token         VARCHAR(40)    NOT NULL,
  client_id            VARCHAR(80)    NOT NULL,
  user_id              VARCHAR(80),
  expires              TIMESTAMP      NOT NULL,
  scope                VARCHAR(4000),
  PRIMARY KEY (access_token)
);

CREATE TABLE oauth_authorization_codes (
  authorization_code  VARCHAR(40)     NOT NULL,
  client_id           VARCHAR(80)     NOT NULL,
  user_id             VARCHAR(80),
  redirect_uri        VARCHAR(2000),
  expires             TIMESTAMP       NOT NULL,
  scope               VARCHAR(4000),
  id_token            VARCHAR(1000),
  PRIMARY KEY (authorization_code)
);

CREATE TABLE oauth_refresh_tokens (
  refresh_token       VARCHAR(40)     NOT NULL,
  client_id           VARCHAR(80)     NOT NULL,
  user_id             VARCHAR(80),
  expires             TIMESTAMP       NOT NULL,
  scope               VARCHAR(4000),
  PRIMARY KEY (refresh_token)
);

CREATE TABLE oauth_users (
  username            VARCHAR(80),
  password            VARCHAR(80),
  first_name          VARCHAR(80),
  last_name           VARCHAR(80),
  email               VARCHAR(80),
  email_verified      BOOLEAN,
  scope               VARCHAR(4000),
  PRIMARY KEY (username)
);

CREATE TABLE oauth_scopes (
  scope               VARCHAR(80)     NOT NULL,
  is_default          BOOLEAN,
  PRIMARY KEY (scope)
);

CREATE TABLE oauth_jwt (
  client_id           VARCHAR(80)     NOT NULL,
  subject             VARCHAR(80),
  public_key          VARCHAR(2000)   NOT NULL
);
~~~

#### 2、server.php
~~~
$dsn      = 'mysql:dbname=my_oauth2_db;host=localhost';
$username = 'root';
$password = '';

// error reporting (this is a demo, after all!)
ini_set('display_errors',1);error_reporting(E_ALL);

// Autoloading (composer is preferred, but for this example let's just do this)
require_once('oauth2-server-php/src/OAuth2/Autoloader.php');
OAuth2\Autoloader::register();

// $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => $username, 'password' => $password));

// Pass a storage object or array of storage objects to the OAuth2 server class
$server = new OAuth2\Server($storage);

// Add the "Client Credentials" grant type (it is the simplest of the grant types)
$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));

// Add the "Authorization Code" grant type (this is where the oauth magic happens)
$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
~~~

#### 3、token.php
~~~~
// 创建令牌控制器
ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once __DIR__ . '/server.php';

$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
~~~~

#### 4、resource.php
~~~~
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
~~~~

#### 5、authorize.php
~~~~
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
$server->handleAuthorizeRequest($request, $response, $isAuthorized);
if ($isAuthorized) {
    $code = substr($response->getHttpHeader('Location'), strpos($response->getHttpHeader('Location'), 'code=') + 5, 40);
    exit("成功！授权代码:{$code}"); // 授权码将在 30 秒后过期
}
$response->send();
~~~~