<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/28
 * Time: 19:47
 */

namespace app\common\bean;


/**
 * 日志
 * Class Logger
 * @package app\common\bean
 */
class Logger
{

    protected $name;

//    protected

    protected function __construct(string $name)
    {
        $this->name = $name;
    }

    static public function newOne($name = '')
    {
        return new self($name);
    }
}