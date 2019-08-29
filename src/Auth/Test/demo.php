<?php
use Common\Component\Auth\AuthClient;
use Common\Component\Auth\AuthServer;

require __DIR__ . '/../AuthClient.php';
require __DIR__ . '/../AuthServer.php';

$accessKey = "test";
$secretAccessKey = "abcdsdsd111";

//请求方式
$method = "POST";
//请求路由
$uri = "/test/index";
//请求类型
$contentType = "application/json";
//请求payload内容
$postJsonData = "{\"a\":\"123\"}";
$postData = json_decode($postJsonData,true);

$authClient = new AuthClient($accessKey,$secretAccessKey);
$authHeader = $authClient->getHeader($postData,$method,$uri,$contentType);
//客户端生成header头信息
print_r($authHeader);

//服务端检验
$requestHeader = $authHeader;
$authServer = new AuthServer($secretAccessKey);
$authResult = $authServer->doAuth($postData,$method,$uri,$contentType,$requestHeader);
//验证结果
var_dump($authResult);







