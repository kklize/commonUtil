<?php
namespace Common\Component\Auth;

/**
 * Class AuthServer
 * ak,sk鉴权服务端
 * @package Common\Component\Auth
 */
class AuthServer
{
    //header请求过来值(x-username) 作为 ak
    public $username;

    public $nonce;
    public $date;
    public $signature;

    //密钥约定sk
    public $secretAccessKey;

    public function __construct($secretAccessKey = "")
    {
        $this->secretAccessKey = $secretAccessKey;
    }

    /**
     * 请求header头内容
     * @param $header
     */
    protected function setInfo($header)
    {
        $this->username = trim($header['x-username']);
        $this->nonce = trim($header['x-nonce']);
        $this->date = trim($header['x-date']);
        $this->signature = trim($header['x-signature']);
    }

    /**
     * @param $data
     * @param $method
     * @param $url
     * @param $contentType
     * @param $header
     *
     * 对请求数据进行 auth鉴权认证
     * @return bool
     */
    public function doAuth($data, $method, $url, $contentType, $header)
    {
        $this->setInfo($header);
        //todo 可结合redis进行指定时间nonce唯一性判断, date时间过期判断..自定义
/*      $keyNonce = $this->nonce;
        $redis = new RedisStore();
        if ($redis->has($keyNonce)) {
            return false;
        } else {*/
        $authClient = new AuthClient();
        $contentMD5 =  $authClient->getContentMD5($data);
        $authClient->data = $this->date;
        $authClient->nonce = $this->nonce;
        $authClient->accessKey = $this->username;
        $authClient->contentMD5 = $contentMD5;
        $authClient->contentType = $contentType;
        $authClient->secretAccessKey = $this->secretAccessKey;
        $sign = $authClient->genSign($method, $url);
        if ($sign == $this->signature) {
            //$redis->put($keyNonce, $this->nonce, 1);
            return true;
        } else {
            return false;
        }
        //}
    }
}
