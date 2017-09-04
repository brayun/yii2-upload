<?php
/*
 *          ┌─┐       ┌─┐
 *       ┌──┘ ┴───────┘ ┴──┐
 *       │                 │
 *       │       ───       │
 *       │  ─┬┘       └┬─  │
 *       │                 │
 *       │       ─┴─       │
 *       └───┐         ┌───┘
 *           │         └──────────────┐
 *           │                        ├─┐
 *           │                        ┌─┘
 *           │                        │
 *           └─┐  ┐  ┌───────┬──┐  ┌──┘
 *             │ ─┤ ─┤       │ ─┤ ─┤
 *             └──┴──┘       └──┴──┘
 *        @Author Ethan <ethan@brayun.com>
 */

namespace brayun;

use DateTime;
use yii\base\Action;

class OssAction extends Action
{

    public $AccessKeyId;

    public $AccessKeySecret;

    public $bucket;

    // 节点
    public $endpoint;

    // 存储目录
    public $dirPath;

    // 回调URL
    public $callbackUrl;

    // 过期时间 默认30秒
    public $expire = 30;

    public $callbackBody = 'bucket=${bucket}&filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}&ext=${imageInfo.format}';

    public $callbackType = 'application/x-www-form-urlencoded';


    public function run() {
        return $this->controller->asJson([
            'accessid' => $this->AccessKeyId,
            'host' => "http://{$this->bucket}.{$this->endpoint}",
            'policy' => $this->policy(),
            'signature' => $this->signature(),
            'expire' => time() + $this->expire,
            'callback' => $this->callback(),
            'dir' => $this->dirPath,
            'filename' => md5(uniqid(microtime(true),true))
        ]);
    }

    /**
     * 生成签名signature
     * @return string
     */
    private function signature() {

        return base64_encode(hash_hmac('sha1', $this->policy(), $this->AccessKeySecret, true));
    }

    /**
     * @return string
     */
    private function policy() {
        $policy = json_encode([
            'expiration' => $this->gmt_iso8601(time() + $this->expire),
            'conditions' => [
                ['content-length-range', 0, 1048576000],
                ['starts-with', '$key', $this->dirPath]
            ]
        ]);
        return base64_encode($policy);
    }

    /**
     * @return string
     */
    private function callback() {
        $callback_param = [
            'callbackUrl' => $this->callbackUrl,
            'callbackHost' => parse_url($this->callbackUrl, PHP_URL_HOST),
            'callbackBody' => $this->callbackBody,
            'callbackBodyType' => $this->callbackType
        ];
        return base64_encode(json_encode($callback_param));
    }

    /**
     * @param $time
     * @return string
     */
    private function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new DateTime($dtStr);
        $expiration = $mydatetime->format(DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }
}
