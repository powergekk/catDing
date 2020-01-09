<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/18
 * Time: 16:17
 */

namespace app\common\controller;


use app\common\base\BaseResp;
use app\common\base\BaseUserSession;
use app\common\base\RespCode;
use app\common\consts\ArrayToObjType;
use app\common\exceptions\ParamsRuntimeException;
use http\Exception\RuntimeException;
use think\controller\Rest;
use think\exception\HttpResponseException;
use think\Response;
use utils\ReflectionUtils;

abstract class BaseRest extends Rest
{

    /**
     * request对象
     * @var \think\Request
     */
    protected $request;


    /**
     * 返回对象
     * @var BaseResp|null
     */
    protected $indexResp;


    /**
     * 当前登陆的用户
     * @var BaseUserSession|null
     */
    protected $user;


    /**
     * 不需要验证登陆,*表示不验证登陆
     * @var array|*
     */
    protected $notCheckLoginAction = [];


    public function __construct()
    {
        parent::__construct();


        //准备基础工作
        $this->_base_construct();
        //完成准备工作后
        $this->_after_base_construct();
        //验证
        $this->_check_constrcut();
        //实例化完成处理
        $this->_after_instance();
    }


    /**
     * 1
     * 准备基础工作
     */
    protected function _base_construct()
    {
        //构造对象
        $this->request = request();
        $this->indexResp = $this->getBaseResp();
        $this->user = $this->getUserSession();
    }


    /**
     * 2
     * 完成基础准备工作后
     * @return mixed
     */
    abstract protected function _after_base_construct();


    /**
     * 3
     * 实例化中，验证内容，
     * @return mixed
     */
    abstract protected function _check_constrcut();


    /**
     * 4
     * #{实例化函数的后面一步}
     * 可以这一步做登陆验证
     * @return mixed
     */
    abstract protected function _after_instance();


    /**
     * #{获取返回对象}
     * @return BaseResp
     */
    abstract protected function getBaseResp(): BaseResp;


    /**
     * #{获取用户session对像}
     * @return BaseUserSession
     */
    abstract protected function getUserSession(): BaseUserSession;




//    protected function checkLogin()
//    {
//        // 登陆验证
//        $dispatch = $this->request->dispatch();
//        $module = $dispatch['module'];
//        //判断module是否在 notCheckLogin 这个里面
//        $controllerNotCheckLoginFlag = isset($this->notCheckLogin[strtolower($module[1])]);
//        //需要验证登陆状态
//        $needCheckLoginFlag = true;
//        if ($controllerNotCheckLoginFlag) {
//            //通过判断action是否可以无需验证登陆
//            if ($this->notCheckLogin[strtolower($module[1])] == "*") {
//                $needCheckLoginFlag = false;
//            } elseif (in_array(strtolower($module[2]), $this->notCheckLogin[strtolower($module[1])])) {
//                $needCheckLoginFlag = false;
//            } else {
//                $needCheckLoginFlag = true;
//            }
//        }
//        if ($needCheckLoginFlag) {
//            if (!$this->user->isLogin()) {
//                $this->indexResp->needLogin();
//                $response = Response::create($this->indexResp->send(), 'json');
//                throw new HttpResponseException($response);
//            }
//        }
//    }

    protected function checkLogin()
    {
        // 登陆验证
        $dispatch = $this->request->dispatch();
        $module = $dispatch['module'];

        //需要验证登陆状态
        $needCheckLoginFlag = true;

        //通过判断action是否可以无需验证登陆
        if ($this->notCheckLoginAction === "*") {
            $needCheckLoginFlag = false;
        } elseif (is_array($this->notCheckLoginAction) && in_array(strtolower($module[2]), $this->notCheckLoginAction)) {
            $needCheckLoginFlag = false;
        }


        if ($needCheckLoginFlag) {
            if (!$this->user->isLogin()) {
                $this->indexResp->needLogin();
                $response = Response::create($this->indexResp->send(), 'json');
                throw new HttpResponseException($response);
            }
        }
    }


    /**
     * 检测当前是否POST,并且参数 是否OK
     */
    protected function checkPost()
    {
        if (!request()->isPost()) {
            $this->indexResp->sets("参数异常", RespCode::ERR_PARAM);

        } else {
            $postData = $this->request->post();
            if (!isset($postData['data']) || !is_array($postData['data'])) {
                $this->indexResp->sets("参数data异常", RespCode::ERR_PARAM);
            } elseif (is_null(request()->post("time"))) {
                $this->indexResp->sets("参数time异常", RespCode::ERR_PARAM);
            } else {
                return true;
            }
        }
        $reponse = Response::create($this->indexResp->send(), 'json');
        throw new HttpResponseException($reponse);
    }


    /**
     * #{获取业务参数}
     * @param null $name KEY
     * @param null $default 默认值
     * @param string $filter 过滤方法
     * @return mixed|null
     */
    /**
     * @param null $name KEY
     * @param null $default 默认值
     * @param string $filter 过滤方法
     * @param string $class 转换成对象
     * @param string $objType 转换类型
     * @return array|mixed|null|object
     * @throws \ReflectionException
     */
    protected function getPostInputData($name = null, $default = null, string $filter = '', string $class='', string $objType=ArrayToObjType::OBJECT)
    {
        $data = $this->request->post('data/a');
        $ret = null;
        if (is_null($name)) {
            $ret = $data;
        } else {
            $ret = isset($data[$name]) ? $data[$name] : $default;
        }
        if (!empty($filter)) {
            foreach (explode(',', $filter) as $f) {
                $ret = call_user_func_array($f, [$ret]);
            }
        }
        //转换到对象
        if(!empty($class)){
            switch ($objType){
                case ArrayToObjType::OBJECT:
                    $ret = ReflectionUtils::arrayToObj($ret, $class, true);
                    break;

                case ArrayToObjType::LIST:
                    $ret = ReflectionUtils::arrayToListObj($ret, $class, true);
                    break;

                case ArrayToObjType::AUTO:
                    $ret = ReflectionUtils::arrayToListOrObj($ret, $class, true);
                    break;

                default:
                    throw new ParamsRuntimeException("getPostInputData param:objType error");
                    break;
            }
        }
        return $ret;
    }

    /**
     * 获取传入的data
     * @return array
     */
    protected function getPostData(): array
    {
        return $this->request->post();
    }


    /**
     * 返回当前页面
     * @return mixed|null
     */
    protected function getPage()
    {
        return $this->getPostInputData("page", 1, 'utils\\PageUtils::formatPage');
    }

    /**
     * 返回页码显示 内容 数
     * @return mixed|null
     */
    protected function getPageSize()
    {
        return $this->getPostInputData("pageSize", 10, 'utils\\PageUtils::formatPageSize');
    }


    /**
     * 处理异常返回
     * @param \Exception $e
     * @param array $data 需要记录的数据信息
     * @return array
     */
    protected function catchExcpetion(\Exception $e, array $data = [])
    {
        //TODO 做日志处理
        return $this->indexResp->exception($e)->send();
    }

}