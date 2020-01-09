<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/15
 * Time: 14:59
 */
namespace response;

use utils\ArrayUtils;

class ReturnResponse implements \JsonSerializable
{


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





    private static $instance;



    protected function __construct()
    {
        $this->data = new \stdClass();
    }


    public function __clone()
    {
        user_error('not allowed clone');
    }


    public function __sleep()
    {
        user_error('not allowed serialize');
    }

    /**
     * @#name 状态
     * @var bool
     */
    private $success = false;


    /**
     * @#name 状态
     * @var int
     */
    private $status = 1;


    /**
     * @#name code码
     * @var string
     */
    private $code = '';


    /**
     * @#name 信息
     * @var string
     */
    private $msg = '';


    /**
     * @#name 内容
     * @var array|object
     */
    private $data;


    /**
     * @#name 时间
     * @var string
     */
    private $time='';

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
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
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
        return $this->msg;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->msg = $message;
    }


    /**
     * @return array|object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array|object $data
     */
    public function setData($data): void
    {
        if(is_array($data)) {
            if(empty($data)) {
                $data = new \stdClass();
            }elseif(ArrayUtils::isAssocArray($data)) {
                $data = ['list' => $data];
                //user_error('data 不接受索引数组');
            }else{
                //
            }
        }elseif(is_object($data)){

        }else{
            user_error('data 只能是array 或 object');
        }


        $this->data = $data;
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


    public function jsonSerialize (){
        $this->setTime(date('Y-m-d H:i:s'));
        return get_object_vars($this);
    }

    public function toArray(){
        return $this->jsonSerialize();
    }


    public static function instance(){
        if(!self::$instance instanceof static){
            self::$instance = new static;
        }
        return self::$instance;
    }



    public static function err(string $msg, string $code='', $data=[]){
        if(empty($code)){
            $code = self::CODE_ERR;
        }
        return self::sets(false, $code, $msg, $data);
    }



    public static function ok( $data=[], string $msg = ''){
        return self::sets(true, self::CODE_SUCCESS, $msg, $data);
    }


    public static function sets(bool $success, string $code, string $msg, $data){
        $response = self::instance();
        $response->setSuccess($success);
        $response->setCode($code);
        $response->setMessage($msg);
        $response->setData($data);
        return $response;
    }


}