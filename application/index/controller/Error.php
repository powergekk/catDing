<?php

namespace app\index\controller;

use app\common\consts\HomeJumpType;
use app\index\base\IndexResp;
use app\index\bean\UserSession;
use utils\ReflectionUtils;

class Error extends Common
{
    /**
     * 不需要验证登陆,*表示不验证登陆
     * @var array|*
     */
    protected $notCheckLoginAction = '*';

    public function _empty($method)//: IndexResp
    {
        return $this->indexResp->urlError()->send();
    }


    /**
     * 4
     * #{实例化函数的后面一步}
     * 可以这一步做登陆验证
     * @return mixed
     */
    protected function _after_instance()
    {
        // TODO: Implement _after_instance() method.
    }
}
