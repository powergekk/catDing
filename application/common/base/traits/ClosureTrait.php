<?php

namespace app\common\base\traits;


use app\common\exceptions\ClassRuntimeException;


/**
 * 闭包执行
 * @author QingXi
 *
 */
trait ClosureTrait
{

    static protected function _closureRun($closure, &$args)
    {
        if (
            (is_array($closure) && is_callable($closure))
            ||
            (is_string($closure) && (function_exists($closure) || is_callable($closure)))
            ||
            $closure instanceof \Closure
        ) {
            return call_user_func_array($closure, $args);
        } else {
            throw new ClassRuntimeException("未找到可用方法: " . $closure);
        }

    }


}