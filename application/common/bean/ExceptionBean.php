<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/17
 * Time: 9:17
 */

namespace app\common\bean;


use think\Lang;

class ExceptionBean
{

    /**
     * 获取错误信息
     * ErrorException则使用错误级别作为错误编码
     * @param  \Exception $exception
     * @return string                错误信息
     */
    public static function getLangMessage(\Exception $exception)
    {
        $message = $exception->getMessage();
        if (IS_CLI) {
            return $message;
        }

        if (strpos($message, ':')) {
            $name = strstr($message, ':', true);
            $message = Lang::has($name) ? Lang::get($name) . strstr($message, ':') : $message;
        } elseif (strpos($message, ',')) {
            $name = strstr($message, ',', true);
            $message = Lang::has($name) ? Lang::get($name) . ':' . substr(strstr($message, ','), 1) : $message;
        } elseif (Lang::has($message)) {
            $message = Lang::get($message);
        }
        return $message;
    }
}