<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/20
 * Time: 19:07
 */

namespace app\common\base;


use utils\SignUtils;

final class ComResp
{

    static protected $_response = [];


    /**
     * 安全不返回字段
     * @var array
     */
    static protected $excludeFile = [
        'excludeFile',
        'parentKey',
        'needSign'
    ];

    /**
     * 状态
     * @var bool
     */
    protected $success = false;

    /**
     * 返回信息
     * @var string
     */
    protected $msg = "";

    /**
     * 状态码
     * @var string
     */
    protected $code = "";


    /**
     * 处理时间
     * @var string
     */
    protected $time;


    /**
     * 接口编号
     * @var  string
     */
    protected $server_id;


    /**
     * 版本
     * @var string
     */
    protected $version;


    /**
     * 用户身份
     * @var string
     */
    protected $parent_id;


    /**
     * 用户签名密钥
     * @var string
     */
    protected $parentKey;


    /**
     * 是否需要签名，主要用在返回的时候
     * @var bool
     */
    protected $needSign = true;

    /**
     * 签名
     * @var string
     */
    protected $sign;


    /**
     * 返回内容
     * @var array
     */
    protected $data = [];

    /**
     * 补充内容
     * @var array
     */
    protected $result = [];

    /**
     * ComResp constructor.
     */
    protected function __construct()
    {

    }


    public function __sleep()
    {
        user_error("response can not serialize");
    }

    public function __clone()
    {
        user_error("response can not clone");
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getMsg(): string
    {
        return $this->msg;
    }

    /**
     * @param string $msg
     */
    public function setMsg(string $msg): void
    {
        $this->msg = $msg;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     * @param string $time
     */
    public function setTime(string $time): void
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getServerId(): string
    {
        return $this->server_id;
    }

    /**
     * @param string $server_id
     */
    public function setServerId(string $server_id): void
    {
        $this->server_id = $server_id;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getParentId(): string
    {
        return $this->parent_id;
    }

    /**
     * @param string $parent_id
     */
    public function setParentId(string $parent_id): void
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return string
     */
    public function getParentKey(): string
    {
        return $this->parentKey;
    }

    /**
     * @param string $parentKey
     */
    public function setParentKey(string $parentKey): void
    {
        $this->parentKey = $parentKey;
    }

    /**
     * @return bool
     */
    public function isNeedSign(): bool
    {
        return $this->needSign;
    }

    /**
     * @param bool $needSign
     */
    public function setNeedSign(bool $needSign): void
    {
        $this->needSign = $needSign;
    }

    /**
     * @return string
     */
    public function getSign(): string
    {
        return $this->sign;
    }

    /**
     * @param string $sign
     */
    public function setSign(string $sign): void
    {
        $this->sign = $sign;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param array $result
     */
    public function setResult(array $result): void
    {
        $this->result = $result;
    }


    /**
     * 转化数组
     * @return array
     */
    protected function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * 转化数组(过虑敏感内容)
     * @return array
     */
    protected function toSafeArray(): array
    {
        $data = get_object_vars($this);
        foreach (self::$excludeFile as $field) {
            if (isset($data[$field])) {
                unset($data[$field]);
            }
        }
        return $data;
    }

    /**
     * 签名,转化数组(过虑敏感内容)
     * @return array
     */
    public function toSafeSignArray(): array
    {
        //开关，是否签名
        if ($this->needSign) {
            $this->sign();
        }
        return $this->toSafeArray();
    }

    /**
     * 转成JSON
     * @return string
     */
    protected function toSafeJson(): string
    {
        return json_encode($this->toSafeArray());
    }

    /**
     * 转成JSON
     * @return self
     */
    protected function sign(): self
    {
        $this->setTime(date('Y-m-d H:i:s'));
        $data = $this->toSafeArray();
        $key = $this->getParentKey();
        $sign = SignUtils::sign($data, $key);
        $this->setSign($sign);
        return $this;
    }


    /**
     * 签名好返回
     * @return string
     */
    public function sendJson(): string
    {
        //开关，是否签名
        if ($this->needSign) {
            $this->sign();
        }
        return $this->toSafeJson();
    }


    /**
     * 获取返回response
     * @param string $name
     * @return ComResp|mixed
     */
    static public function instance($name = 'default')
    {
        if (!isset(self::$_response[$name])) {
            self::$_response[$name] = new self;
        }
        return self::$_response[$name];
    }


    /**
     * 设定状态为成功
     */
    public function ok($data = null)
    {
        return $this->sets('', RespCode::OK, $data);
    }

    /**
     * 设定状态为失败
     */
    public function err($msg = '', $data = null)
    {
        return $this->sets($msg, RespCode::ERR, $data);
    }

    /**
     * 设定状态
     */
    public function sets($msg = '', $code = '', $data = null)
    {
        $this->setSuccess($code == RespCode::OK);
        isset($msg) && $this->setMsg($msg);
        isset($code) ? $this->setCode($code) : $this->setCode(RespCode::OK);
        isset($data) && $this->setData($data);
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->sendJson();
    }
}