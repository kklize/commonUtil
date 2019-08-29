<?php
namespace Common\Component\Auth;

use stdClass;

/**
 * Class AuthClient
 * ak,sk鉴权客户端
 *
 * @package Common\Component\Auth
 */
class AuthClient
{
    //密钥约定ak
    public $accessKey;
    //密钥约定sk
    public $secretAccessKey;
    //请求payload加密串
    public $contentMD5;
    //请求header头类型
    public $contentType;
    //请求生成的时间
    public $data;
    //客户端生成32位随机字符串,请求体中5分钟内不能重复，重复则回复nonce重复，重新请求
    public $nonce;
    /**
     * StringToSign生成规则如下(没有的部分不用拼，如响应头中，没有uri，则StringToSign中不用拼URI项)：
     * StringToSign =  Method + "\n"
     * + URI + "\n"
     * + Content-MD5 + "\n"
     * + Content-Type + "\n"
     * + CanonicalizedHeaders)
     */
    public $signature;

    /**
     * AuthClient constructor.
     *
     * @param string $accessKey
     * 约定ak值
     * @param string $secretAccessKey
     * 约定sk值
     */
    public function __construct($accessKey = "", $secretAccessKey = "")
    {
        $this->accessKey = $accessKey;
        $this->secretAccessKey = $secretAccessKey;
    }

    /**
     * @param $data
     * @param $method
     * @param $url
     * @param $contentType
     *
     * 生成相关数据
     */
    private function genInfo($data, $method, $url, $contentType)
    {
        $this->contentMD5 = $this->getContentMD5($data);
        $this->data = $this->getDate();
        $this->nonce = $this->getNonce();
        $this->contentType = $contentType;
        $this->signature = $this->genSign($method, $url);
    }

    /**
     * @param $data
     * @param $method
     * @param $url
     * @param $contentType
     *
     * 生成此次请求想要的header头信息
     * @return array
     */
    public function getHeader($data, $method, $url, $contentType)
    {
        $this->genInfo($data, $method, $url, $contentType);
        $headers = [
            'Content-MD5' => $this->contentMD5,
            'x-date' => $this->data ,
            //服务端验证的ak 即是x-username值
            'x-username' => $this->accessKey,
            'x-nonce' =>  $this->nonce,
            'x-signature' => $this->signature
        ];
        return $headers;
    }

    /**
     * @param $method
     * @param $url
     *
     * 生成sign验签
     * @return string
     */
    public function genSign($method, $url)
    {
        $signature = base64_encode(hash_hmac(
            'sha256',
            $this->getStrSign($method, $url, $this->contentMD5, $this->contentType),
            strtolower(md5($this->secretAccessKey)),
            256
        ));
        return $signature;
    }

    /**
     * StringToSign生成规则如下(没有的部分不用拼，如响应头中，没有uri，则StringToSign中不用拼URI项)：
     * StringToSign =  Method + "\n"
     * + URI + "\n"
     * + Content-MD5 + "\n"
     * + Content-Type + "\n"
     * + CanonicalizedHeaders)
     * @return string
     */
    private function getStrSign($method, $url, $contentMD5, $contentType)
    {
        $method = strtoupper($method);
        $canonicalized_headers = $this->getCanonicalizedHeaders();
        $data = [
            $method,
            $url,
            $contentMD5,
            $contentType,
        ];
        foreach ($canonicalized_headers as $k => $v) {
            $data[] = $k . ":" . $v;
        }
        $str = "";
        foreach ($data as $item) {
            $str .= $item . "\n";
        }
        return $str;
    }

    /**
     * 以x-为前缀的的Headers，但是不包括x-pcs-signature
     * Header名称全部小写，值前后应不包含空格
     * Header的名称和值之间用“:”相隔，组成一个完整的header
     * 根据header名称的字符顺序，将header从小到大进行字典排序
     * 每个header之后跟一个“\n”
     * @return array
     */
    private function getCanonicalizedHeaders()
    {
        $data = [
            'x-date' => $this->data ,
            'x-username' => $this->accessKey,
            'x-nonce' =>  $this->nonce
        ];
        ksort($data);
        return $data;
    }

    /**
     *
     * @param $param
     * 请求报文(数组格式)
     * @return string
     * 加密后内容
     */
    public function getContentMD5($param)
    {
        if (empty($param)) {
            $param = new stdClass();
        }
        $json = json_encode($param, JSON_UNESCAPED_UNICODE);
        $new_json = str_replace("\\/", "/", $json);
        return base64_encode(md5($new_json, true));    //这里需要json_encode 否则会为空
    }

    /**
     * 请求生成的时间，与服务器本地时间差超过5分钟，认为鉴权失败。
     * @return false|string
     */
    private function getDate()
    {
        return date('Y-m-d\TH:i:m\Z', time());
    }

    /**
     * 客户端生成32位随机字符串,请求体中5分钟内不能重复，重复则回复nonce重复，重新请求
     * @return string
     */
    private function getNonce()
    {
        return md5(uniqid());
    }
}
