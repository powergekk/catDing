<?php

namespace app\common\base\traits;

use app\common\exceptions\SingleInstanceRuntimeException;

trait InstanceTrait
{

    /**
     * 重置实例并返回
     *
     * @var string
     */
    public static $RESET_INSTANCE = "reset instance";

    /**
     * 返回一个新的实例，但不重置
     *
     * @var string
     */
    public static $RETURN_NEW_INSTANCE = "return new instance";

    /**
     * 返回已存在的实例，如果不存在，则创建
     *
     * @var string
     */
    public static $STATIC_INSTANCE = "static instance";

    protected static $instance = null;

    /**
     *
     * @param String $instanceFlag
     * @return \app\common\base\traits\InstanceTrait|static
     */
    static public final function instance($instanceFlag = "static instance")
    {
        switch ($instanceFlag) {
            case static::$RETURN_NEW_INSTANCE:
                $static = new static(); // static::newIns();
                $static->_after_instance();
                return $static;
                break;

            case static::$RESET_INSTANCE:
                static::$instance = new static(); // static::newIns();
                static::$instance->_after_instance();
                return static::$instance;
                break;

            case static::$STATIC_INSTANCE:
                if (!static::$instance instanceof static) {
                    static::$instance = new static();
                }
                //
                static::$instance->_after_instance();
                //
                return static::$instance;
                break;

            default:
                // TODO 异常
                return new static(); // static::newIns();
                break;
        }
    }

    /**
     * 返回新的实例
     *
     * @param unknown $afterParam
     * @return \app\common\base\traits\InstanceTrait
     */
    static public final function newStatic($afterParam = null)
    {
        $obj = new static();
        $obj->_after_instance($afterParam);
        return $obj;
    }

    protected final function __construct()
    {

    }

    /**
     * 实例化后执行方法，子类可修改
     */
    protected function _after_instance()
    {
    }

    public final function __clone()
    {
        throw new SingleInstanceRuntimeException("the class instance can not clone!");
    }

    // 静态调用
    public static function __callStatic($method, $params)
    {
        if (is_null(static::$instance)) {
            self::$instance = new static();
        }
        $call = substr($method, 1);
        if (0 === strpos($method, '_') && is_callable([
                static::$instance,
                $call
            ])) {
            return call_user_func_array([
                static::$instance,
                $call
            ], $params);
        } else {
            throw new \RuntimeException("method not exists:" . $method);
        }
    }
}