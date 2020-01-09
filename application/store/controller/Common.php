<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 16:00
 */

namespace app\store\controller;

use app\common\base\BaseResp;
use app\common\base\BaseUserSession;
use app\common\controller\BaseRest;
use app\store\base\IndexResp;
use app\store\bean\UserSession;

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


    /**
     * 不需要验证登陆,*表示不验证登陆
     * @var array|*
     */
    protected $notCheckLoginAction = [];


    protected final function getBaseResp(): BaseResp
    {
        // TODO: Implement getBaseResp() method.
        return IndexResp::instance();
    }

    protected final function getUserSession(): BaseUserSession
    {
        // TODO: Implement getUserSession() method.
        return $this->getStoreUserSession();
    }


    /**
     * @return UserSession|null
     */
    protected final function getStoreUserSession(): UserSession
    {
        return UserSession::instance();
    }

    /**
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
    protected function getPlateformId(): int
    {
        return IndexResp::instance()->getPlateformId();
    }

    /**
     * 获取当前登陆的用户ID
     * @return int
     */
    protected function getUserId(): int
    {
        return $this->user->getId() > 0 ? $this->user->getId() : -1;
    }


    /**
     * 2
     * 完成基础准备工作后
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