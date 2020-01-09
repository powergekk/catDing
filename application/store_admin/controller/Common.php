<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/29
 * Time: 16:04
 */

namespace app\store_admin\controller;


use app\store_admin\dto\ResponseDto;
use think\controller\Rest;
use think\Request;

abstract class Common extends Rest
{

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ResponseDto
     */
    protected $response;



    final public function __construct(Request $request)
    {
        parent::__construct();


        //准备基础工作
        $this->_base_construct($request);
        //完成准备工作后
        $this->_after_base_construct();
        //验证
        $this->_check_constrcut();
        //实例化完成处理
        $this->_after_instance();
    }

    protected function _base_construct(Request $request){
        $this->request = $request;
        $this->response = ResponseDto::instance();
        return;
    }


    protected function _after_base_construct(){
        return;
    }

    protected function _check_constrcut(){
        return;
    }


    abstract protected function _after_instance(...$args);

    /**
     * Session管理
     * @param string|array  $name session名称，如果为数组表示进行session设置
     * @param mixed         $value session值
     * @return mixed
     */
    final protected function session($name, $value = '')
    {
        return session($name, $value, null);
    }

}