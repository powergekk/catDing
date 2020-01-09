<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 9:12
 */

namespace app\index\cache;


use app\common\base\traits\InstanceTrait;
use app\common\cache\ComCache;
use think\cache\Driver;


/**
 * Class CacheObj
 * @package app\index\cache
 */
class CacheBean
{
    use InstanceTrait;

    /**
     * @var Driver
     */
    private $cache;


    /**
     * 获取实例
     * @return Driver
     */
    protected function getCatchInstance()
    {
        if (is_null($this->cache)) {
            $this->cache = ComCache::init();
        }
        return $this->cache;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
        if (method_exists($this->getCatchInstance(), $name)) {
            return call_user_func_array([$this->getCatchInstance(), $name], $arguments);
        } else {
            user_error('class: [' . get_class($this->getCatchInstance()) . '] has not method: [' . $name . ']');
        }
    }


    /**
     * 读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $this->getCatchInstance()->get($name, $default);
    }


    /**
     * 写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param int $expire 有效时间 0为永久
     * @return boolean
     */
    public function set(string $name, $value, $expire = null)
    {
        return $this->getCatchInstance()->set($name, $value, $expire);
    }


    /**
     * 自增缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function inc(string $name, int $step = 1)
    {
        return $this->getCatchInstance()->inc($name, $step);
    }

    /**
     * 自减缓存（针对数值缓存）
     * @access public
     * @param string $name 缓存变量名
     * @param int $step 步长
     * @return false|int
     */
    public function dec(string $name, int $step = 1)
    {
        return $this->getCatchInstance()->dec($name, $step);
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolean
     */
    public function rm(string $name)
    {
        return $this->getCatchInstance()->rm($name);
    }

    /**
     * 清除缓存
     * @access public
     * @param string $tag 标签名
     * @return boolean
     */
    public function clear($tag = null)
    {
        return $this->getCatchInstance()->clear($tag);

    }


    /**
     * 切换缓存类型 需要配置 cache.type 为 complex
     * @access public
     * @param  string $name 缓存标识
     * @return Driver
     */
    public function store(string $name = '')
    {

        return $this->getCatchInstance()->store($name);
    }

    /**
     * 判断缓存是否存在
     * @access public
     * @param  string $name 缓存变量名
     * @return bool
     */
    public function has(string $name)
    {
        return $this->getCatchInstance()->has($name);
    }


    /**
     * 读取缓存并删除
     * @access public
     * @param string $name 缓存变量名
     * @return mixed
     */
    public function pull($name)
    {
        return $this->getCatchInstance()->pull($name);
    }

    /**
     * 如果不存在则写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value 存储数据
     * @param int $expire 有效时间 0为永久
     * @throws \throwable
     * @return mixed
     */
    public function remember(string $name, $value, int $expire = 0)
    {
        return $this->getCatchInstance()->remember($name, $value, $expire);
    }

    /**
     * 返回句柄对象，可执行其它高级方法
     *
     * @access public
     * @return object
     */
    public function handler()
    {
        return $this->getCatchInstance()->handler();
    }
}