<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/29
 * Time: 16:02
 */

namespace app\store_admin\dto;


use app\common\base\traits\InstanceTrait;
use app\common\bean\ExceptionBean;

/**
 * 中间代理层返回给外界的response返回值协议
 * Class ResponseDto
 * @package app\store_admin\dto
 */
class ResponseDto implements \JsonSerializable
{
    use InstanceTrait;

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
        return $this->toArray();
    }

    /**
     * @#name 成功
     * @var string
     */
    const CODE_SUCCESS = '0X0000';


    /**
     * @#name 系统异常
     * @var string
     */
    const CODE_FAIL = '0F0001';

    /**
     * @#name 业务错误
     * @var string
     */
    const CODE_ERR = '0E0001';


    /**
     * @#name 参数错误
     * @var string
     */
    const CODE_PARAMS_ERR = '0E0002';


    /**
     * @#name URL错误
     * @var string
     */
    const CODE_URL_ERR = '0E0003';


    /**
     * @#name 登录异常
     * @var string
     */
    const CODE_LOGIN_ERR = '0L0001';


    /**
     * @#name 未登录
     * @var string
     */
    const CODE_NO_LOGIN = '0N0001';


    /**
     * @#name token错误
     * @var string
     */
    const CODE_TOKEN_ERROR = '0T0001';


    /**
     *
     */

    private $success = false;


    private $code = '0X0001';


    private $message = '';


    private $data;


    private $server = '';

    private $time = '';

    /**
     * @return mixed
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * @param mixed $success
     */
    public function setSuccess($success): void
    {
        $this->success = $success;
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
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message=''): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @param string $server
     */
    public function setServer(string $server): void
    {
        $this->server = $server;
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
    public function setTime(string $time=''): void
    {
        if(empty($time)){
            $this->time = date('Y-m-d H:i:s');
        }else{
            $this->time = $time;
        }

    }


    private function setErrServer(string $server = '')
    {
        empty($server) && $server = $this->server;
        $this->setServer($server);
    }

    private function set(bool $success, string $code, string $msg, $data)
    {
        $this->setTime(date('Y-m-d, H:i:s'));
        $this->setSuccess($success);
        $this->setCode($code);
        $this->setMessage($msg);
        $this->setData($data);
        return $this;
    }

    public function setALl(\utils\HttpResponse $httpResponse, string $server = '')
    {
        $retData = json_decode($httpResponse->getBody(), true);
        if(isset($retData['data']['token'])) unset($retData['data']['token']);
        $this->setTime(date('Y-m-d, H:i:s'));
        $this->setData($retData['data']);
        $this->setMessage($retData['msg']);
        $this->setTime($retData['time']);
        $this->setSuccess($retData['success']);
        $this->setCode($retData['code']);
        $this->setServer($server);
        return $this;
    }

    public function ok(string $msg = '', $data = [], string $server = '')
    {
        $this->setErrServer($server);
        return $this->set(true, self::CODE_SUCCESS, $msg, $data);
    }

    public function err(string $msg, string $code, $data = [], string $server = '')
    {
        $this->setErrServer($server);
        return $this->set(false, $code, $msg, $data);
    }

    public function toArray(){
        return get_object_vars($this);
    }

    public function tokenError(string $msg = '', string $server = '')
    {
        $this->setErrServer($server);
        $this->set(false, self::CODE_TOKEN_ERROR, trim($msg) === ''? 'token错误！' : $msg , null);
        return $this;
    }

    public function dataError(string $msg = '', string $server = '')
    {
        $this->setErrServer($server);
        $this->set(false, self::CODE_PARAMS_ERR, trim($msg) === ''? 'data参数错误！' : $msg, null);
        return $this;
    }

    public function responseError(string $msg = '', string $server = '')
    {
        $this->setErrServer($server);
        $this->set(false, self::CODE_PARAMS_ERR, trim($msg) === '' ? '返回值结构错误！' : $msg, null);
        return $this;
    }

    /**
     * 异常返回
     * @param \Exception $e
     * @return $this
     */
    public function exception(\Exception $e)
    {
//        //TODO 日志处理
        $message = ExceptionBean::getLangMessage($e);
//        if ($e instanceof RouteNotFoundException) {
//            $this->urlError($message);
//        } elseif ($e instanceof PlateformRuntimeException) {
//            $this->plateformError();
//        } elseif ($e instanceof ParamsRuntimeException) {
//            $this->paramsError($e->getMessage());
//        } else {
//            $this->sets($message, RespCode::RUN_ERR);
//        }

//        $this->tokenError();
        $this->message = $message;

        return $this;
    }
}