<?php
// +----------------------------------------------------------------------
// | Dingding.php
// +----------------------------------------------------------------------
// | Description:
// +----------------------------------------------------------------------
// | Time: 2020/3/3 下午3:11
// +----------------------------------------------------------------------
// | Author: wufly <wfxykzd@163.com>
// +----------------------------------------------------------------------

namespace Wufly\Dingding;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class Dingding
{
    protected $secret;
    protected $token;
    protected $at;

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    protected $uri;

    const DingDing_URL = 'https://oapi.dingtalk.com/robot/send?access_token=';

    /**
     * @param mixed $uri
     */
    public function setUri()
    {
        $this->uri = self::DingDing_URL . $this->getToken() . '&' . $this->sign();
    }

    /**
     * @return mixed
     */
    public function getUri()
    {
        return $this->uri;
    }

    public function __construct($type = null)
    {
        $this->secret = Config::get('dingding.secret' . $type);
        $token = Config::get('dingding.access_token' . $type);
        $this->setToken($token);
        $this->setUri();
    }

    // 签名
    public function sign()
    {
        list($msec, $sec) = explode(' ', microtime());
        $timestamp = (float)sprintf('%.0f', ((float)$msec + (float)$sec) * 1000);
        $string_to_sign = $timestamp . "\n" . $this->secret;
        $signature = hash_hmac('sha256', $string_to_sign, $this->secret, true);
        $urlencode_signature = urlencode(base64_encode($signature));
        return 'timestamp=' . $timestamp . '&sign=' . $urlencode_signature;
    }

    public function text($text)
    {
        // 一段时间有相同提示就不发请求
        $cacheTime = Config::get('dingding.default_cache_time');
        if (Cache::has('dingding-notice:' . $text)) {
            return;
        }
        Cache::put('dingding-notice:' . $text, 1, $cacheTime);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUri());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=utf-8'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->TextData($text)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function TextData($text)
    {
        $res = [
            'msgtype' => 'text',
            'text'    => [
                'content' => $text
            ]
        ];
        if ($this->at) {
            $res['at'] = $this->at;
        }
        return $res;
    }

    public function setAt($at)
    {
        if (!$at) {
            $this->at = [
                "atMobiles" => [
                ],
                "isAtAll"   => true
            ];
        } elseif (is_array($at)) {
            $this->at = [
                "atMobiles" => $at,
                "isAtAll"   => false
            ];
        } else {
            $this->at = [
                "atMobiles" => [$at],
                "isAtAll"   => false
            ];
        }
    }
}
