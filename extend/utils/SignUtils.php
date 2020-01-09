<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/20
 * Time: 15:11
 */

namespace utils;


class SignUtils
{
    static protected $excludeField = [
        'sign',
        'result'
    ];

    /**
     * 签名
     * @param $data
     * @return string
     */
    static public function sign($data, String $key = null, String $type = 'md5')
    {
        $signStr = static::paramToString($data);

        return static::encryption($signStr, $key, $type);
    }

    /**
     * 将内容转换成字符串
     * @param $data
     * @return string
     */
    static public function paramToString($data)
    {
        if (is_object($data)) {
            return static::formatToString(get_class_vars($data));
        } elseif (is_array($data)) {
            return static::formatToString($data);
        } else {
            user_error("暂时只支持对象和数组签名");
        }
    }


    /**
     * 验证签名
     * @param $param
     * @param String|null $key
     * @param String $type
     * @return bool
     */
    static public function checkSign($param, String $key = null, String $type = 'md5')
    {
        if (is_object($param)) {
            $data = get_class_vars($param);
        } else {
            $data = $param;
        }
        if (!isset($data['sign'])) {
            return false;
        }
        return static::sign($data, $key, $type) === $data['sign'];
    }

    static protected function formatToString(array $param, Int $level = 1)
    {
        $str = "";
        $data = $param;
        ksort($data);
        foreach ($data as $key => $val) {
            if ($level == 1 && in_array(strtolower($key), static::$excludeField)) {
                continue;
            }
            if (is_object($val)) {
                $str .= $key . "=" . static::formatToString(get_class_vars($val), $level + 1) . "&";
            } elseif (is_array($val)) {
                $str .= $key . "=" . static::formatToString($val, 2) . "&";
            } elseif (is_null($val)) {
                $str .= $key . "=null" . "&";
            } elseif (is_bool($val)) {
                $str .= $key . "=" . ($val == true ? "true" : "false") . "&";
            } else {
                $str .= $key . "=" . $val . "&";
            }
        }
        return $str;
//        return "[" . implode(",", $strArr) . "]";
    }


    /**
     * 签名方法
     * @param String $str
     * @param String $key
     * @param String $type
     * @return mixed
     */
    static protected function encryption(String $str, String $key, String $type = 'md5')
    {
        return call_user_func_array($type, [$str . "+" . $key]);
    }
}