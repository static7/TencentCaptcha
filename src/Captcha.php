<?php
/**
 * Description of Captcha.php.
 * User: static7 <static7@qq.com>
 * Date: 2018/4/28 9:25
 */

namespace static7;

use think\Exception;
use think\facade\{
    Config, Request
};

class Captcha
{
    //票据
    private $ticket;
    //随机字符串
    private $randstr;
    //IP
    private $ip;
    //aid
    private $aid;
    //AppSecretKey
    private   $AppSecretKey;
    //url
    protected $url = 'https://ssl.captcha.qq.com/ticket/verify';

    /**
     * Captcha constructor.
     * * @param array $config
     * * @throws Exception
     */
    public function __construct(array $config = [])
    {
        if (empty($config)) {
            $config = Config::get('config.tencent_captcha');
        } else if (empty($config['aid']) || empty($config['AppSecretKey'])) {
            throw new Exception('配置 aid 和 AppSecretKey 不能为空');
        }
        $this->aid          = $config['aid'];
        $this->AppSecretKey = $config['AppSecretKey'];
    }

    /**
     * 获取配置
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 设置票据
     * @author staitc7 <static7@qq.com>
     * @param string $ticket 票据
     * @return mixed
     */
    public function setTicket($ticket = '')
    {
        empty($this->ticket) && $this->ticket = $ticket;
        return $this;
    }

    /**
     * 获取票据
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function getTicket()
    {
        return $this->ticket;
    }

    /**
     * 设置ip
     * @author staitc7 <static7@qq.com>
     * @param string $ip IP
     * @return mixed
     */
    public function setIp($ip = '')
    {
        empty($this->ip) && $this->ip = $ip ?: Request::ip(0, true);
        return $this;
    }

    /**
     * 获取ip
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip ?: Request::ip(0, true);
    }

    /**
     * 设置随机字符串
     * @author staitc7 <static7@qq.com>
     * @param string $string
     * @return mixed
     */
    public function setRandstr($string = '')
    {
        empty($this->randstr) && $this->randstr = $string;
        return $this;
    }

    /**
     * 获取随机字符串
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function getRandstr()
    {
        return $this->randstr;
    }


    /**
     * 验证
     * @author staitc7 <static7@qq.com>
     * @param string $ticket 票据
     * @param string $randstr
     * @param string $ip ip
     * @return mixed
     * @throws Exception
     */
    public function verify($ticket = null,$randstr=null, $ip = null)
    {
        $ticket && $this->setTicket($ticket);
        $ip && $this->setip($ip);
        $randstr && $this->setRandstr($randstr);
        $param = [
            'aid' => $this->aid,
            'AppSecretKey' => $this->AppSecretKey,
            'Ticket' => $this->getTicket(),
            'Randstr' => $this->getRandstr(),
            'UserIP' => $this->getIp()
        ];
        $data = $this->sendRequest($param);
        return json_decode($data,true);
    }

    /**
     * 请求
     * @author staitc7 <static7@qq.com>
     * @param string $url
     * @param array  $param
     * @param string $method
     * @return mixed
     * @throws Exception
     */
    public function sendRequest($param = [], $url = '', $method = 'get')
    {
        if ($url) {
            $this->url = $url;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, Request::header('user-agent'));
        $data = http_build_query($param ?? '');
        if (strtolower($method) == 'get') {
            curl_setopt($ch, CURLOPT_URL, $this->url . '?' . $data);
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $error = sprintf("curl[%s] error[%s]", $this->url, curl_errno($ch) . ':' . curl_error($ch));
            throw new Exception($error);
        }
        curl_close($ch);
        return $result;
    }
}