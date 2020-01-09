<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/20
 * Time: 19:07
 */

namespace app\index\base;

use app\common\base\BaseResp;
use app\common\base\RespCode;
use app\common\bean\ExceptionBean;
use app\common\exceptions\ParamsRuntimeException;
use app\common\exceptions\PlateformRuntimeException;
use app\common\model\PlateformModel;
use think\exception\RouteNotFoundException;
use think\Model;
use utils\ArrayUtils;

final class IndexResp implements BaseResp
{

    /**
     *
     * @var null|indexResp
     */
    protected static $_instance = null;

    /**
     * 状态
     *
     * @var bool
     */
    protected $success = false;

    /**
     * 返回信息
     *
     * @var string
     */
    protected $msg = "";

    /**
     * 状态码
     *
     * @var string
     */
    protected $code = "";

    /**
     * 处理时间
     *
     * @var string
     */
    protected $time;

    /**
     * 版本
     *
     * @var string
     */
    protected $version;


    /**
     * 平台ID
     *
     * @var int
     */
    protected $plateformId;

    /**
     *  平台编号
     * @var string
     */
    protected  $plateformNo;
    /**
     * 返回内容
     *
     * @var array
     */
    protected $data = [];

    /**
     * 补充内容
     *
     * @var array
     */
    protected $result = [];

    /**
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     *
     * @param bool $success
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     *
     * @return string
     */
    public function getMsg(): string
    {
        return $this->msg;
    }

    /**
     *
     * @param string $msg
     */
    public function setMsg(string $msg): void
    {
        $this->msg = $msg;
    }

    /**
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     *
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     *
     * @return string
     */
    public function getTime(): string
    {
        return $this->time;
    }

    /**
     *
     * @param string $time
     */
    public function setTime(string $time): void
    {
        $this->time = $time;
    }

    /**
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     *
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getPlateformId(): int
    {
        return $this->plateformId;
    }

    /**
     * @param int $plateformId
     */
    public function setPlateformId(int $plateformId): void
    {
        $this->plateformId = $plateformId;
    }


    /**
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }


    /**
     * @param array|Model|mix $data
     * @throws \think\Exception
     */
    public function setData($data): void
    {
        if ($data instanceof Model) {
            $this->data = $data->toArray();
        } elseif (is_array($data) && ArrayUtils::isAssocArray($data)) {
            $this->data = ['list' => $data];
        } elseif (is_array($data) && !ArrayUtils::isAssocArray($data)) {
            $this->data = $data;
        } else {
            $this->data = ['result' => $data];
        }

    }

    /**
     *
     * @return array
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     *
     * @param array $result
     */
    public function setResult(array $result): void
    {
        $this->result = $result;
    }

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
     * 转化数组
     *
     * @return array
     */
    public function toArray(): array
    {
        $res = get_object_vars($this);
        if (empty($res['data'])) {
            $res['data'] = new \stdClass();
        } elseif (ArrayUtils::isAssocArray($res['data'])) {
            $res['data'] = [
                'list' => $res['data']
            ];
        }
        if (empty($res['result'])) {
            $res['result'] = new \stdClass();
        } elseif (ArrayUtils::isAssocArray($res['result'])) {
            $res['result'] = [
                'list' => $res['result']
            ];
        }
        return $res;
    }

    /**
     *  获取平台编号
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPlateformNo()
    {
        if(isset($this->plateformNo) && !empty($this->plateformNo)) {
            return $this->plateformNo;
        }else{
            $platformNo = PlateformModel::field('plateform_no')->find();
            $this->plateformNo = $platformNo->plateform_no;

            return $this->plateformNo;
        }
    }

    /**
     * 转化json
     *
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * 签名好返回
     *
     * @return string
     */
    protected function sendJson(): string
    {
        return $this->toJson();
    }

    /**
     * 获取返回response
     *
     * @return IndexResp|mixed
     */
    static public function instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
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
     * 返回
     *
     * @param string $type
     * @return array
     */
    public function send(string $type = 'json')
    {
        $this->setTime(date('Y-m-d H:i:s'));
        return $this->toArray();
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->sendJson();
    }

    /**
     * 返回登录验证异常，需要重新登录
     * @return \app\index\base\IndexResp
     */
    public function needLogin()
    {
        $this->sets("token验证异常，请重新登录!", RespCode::ERR_TOKEN);
        return $this;
    }

    /**
     * 平台请求异常
     * @return \app\index\base\IndexResp
     */
    public function plateformError()
    {
        $this->sets("平台无效或异常，请刷新重试!", RespCode::ERR_PLATEFORM_ID);
        return $this;
    }


    /**
     * URL异常
     * @param string $msg
     * @return $this
     */
    public function urlError(string $msg = '')
    {
        $this->sets(trim($msg) === '' ? '当前URL:[' . request()->url() . ']不存在或异常' : trim($msg), RespCode::RUN_URL);
        return $this;
    }


    /**
     * 参数异常
     * @param string $msg
     * @return $this
     */
    public function paramsError(string $msg = '')
    {
        $this->sets($msg, RespCode::ERR_PARAM);
        return $this;
    }

    /**
     * 异常返回
     * @param \Exception $e
     * @return $this
     */
    public function exception(\Exception $e)
    {
        //TODO 日志处理
        $message = ExceptionBean::getLangMessage($e);
        if ($e instanceof RouteNotFoundException) {
            $this->urlError($message);
        } elseif ($e instanceof PlateformRuntimeException) {
            $this->plateformError();
        } elseif ($e instanceof ParamsRuntimeException) {
            $this->paramsError($e->getMessage());
        } else {
            $this->sets($message, RespCode::RUN_ERR);
        }

        return $this;
    }

    /**
     * 判断是否刚初始化
     * @return bool
     */
    public function isInit()
    {
        return !isset($this->plateformId);
    }
}