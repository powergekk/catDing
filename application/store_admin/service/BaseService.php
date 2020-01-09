<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/29
 * Time: 15:58
 */

namespace app\store_admin\service;


use app\common\base\traits\InstanceTrait;
use app\common\service\ServiceAbstract;

abstract class BaseService extends ServiceAbstract
{
    use InstanceTrait;

    //必须带上
    protected static $instance;

}