<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 16:00
 */

namespace app\index\controller;

use app\common\base\BaseResp;
use app\common\base\BaseUserSession;
use app\common\controller\BaseRest;
use app\index\base\IndexResp;
use app\index\bean\UserSession;

abstract class Common extends BaseRest
{


    /**
     * 返回对象
     * @var IndexResp|null
     */
    protected $indexResp;


    /**
     * 当前登陆的用户
     * @var UserSession|null
     */
    protected $user;


    protected final function getBaseResp(): BaseResp
    {
        // TODO: Implement getBaseResp() method.
        return IndexResp::instance();
    }

    protected final function getUserSession(): BaseUserSession
    {
        // TODO: Implement getUserSession() method.
        return UserSession::instance();
    }

    /**
     * 验证内容
     * @return mixed|void
     */
    protected final function _check_constrcut()
    {
        //验证登陆
        $this->checkLogin();
    }


    /**
     * 获取当前的平台ID
     * @return int
     */
    protected final function getPlateformId(): int
    {
        return IndexResp::instance()->getPlateformId();
    }

    /**
     * 获取当前登陆的用户ID
     * @return int
     */
    protected final function getUserId(): int
    {
        return $this->user->getId() > 0 ? $this->user->getId() : -1;
    }

    /**
     * 2
     * #{完成基础准备工作后}
     * @return mixed
     */
    protected final function _after_base_construct()
    {
        // TODO: Implement _after_base_construct() method.
    }

    /**
     * 锁定实例方式
     * Common constructor.
     */
    public final function __construct()
    {
        parent::__construct();
    }
}