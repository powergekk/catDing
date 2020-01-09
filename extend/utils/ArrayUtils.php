<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 18:26
 */

namespace utils;


/**
 * 数组工具类
 * Class ArrayUtils
 * @package utils
 */
class ArrayUtils
{

    /**
     * 验证是否索引数组(非关联数组)
     * @param array $array
     * @return bool
     */
    static public function isAssocArray(array $array): bool
    {
        $index = 0;
        foreach (array_keys($array) as $key) {
            if ($index++ != $key || !is_numeric($key)) return false;
        }
        return true;
    }


    /**
     * 获取数组中的值
     * @param array $array
     * @param string $name
     * @param null $default
     * @param string $filters
     * @return mixed|null
     */
    static public function getVal(array $array, string $name, $default = null, string $filters = '')
    {
        if (isset($array[$name])) {
            $data = $array[$name];
            if (!empty($filters)) {
                $filterArr = explode(",", $filters);
                foreach ($filterArr as $filter) {
                    $data = call_user_func_array($filter, [$data]);
                }
            }
            return $data;
        } else {
            return $default;
        }
    }


    /**
     * 设定数组中的值
     * @param array $array
     * @param string $name
     * @param null $val
     * @param string $filters
     * @return bool
     */
    static public function setVal(array &$array, string $name, $val = null, string $filters = '')
    {
        $data = $val;
        if (!empty($filters)) {
            $filterArr = explode(",", $filters);
            foreach ($filterArr as $filter) {
                $data = call_user_func_array($filter, [$data]);
            }
        }
        $array[$name] = $data;
        return true;
    }

}