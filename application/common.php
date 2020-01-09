<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 获取当前请求的HOST
 * @return mixed
 */
function rootHost()
{
    return $_SERVER['SERVER_NAME'];

}


/**
 * 获取当前系统的URL路径
 * @return string
 */
function rootPath()
{
    return str_replace(DS, '/', dirname($_SERVER['SCRIPT_NAME']));
}


/**
 * 获取当前路由PATH
 * @return string
 */
function rootPathUrl()
{
    return rootHost() . rootPath();
}


/**
 * @param string $publicFile
 * @param string $version
 * @return string
 */
function staticFile(string $publicFile = '', string $version = '')
{
    static $rootPathUrl = null;
    if (is_null($rootPathUrl)) {
        $rootPathUrl = rootPathUrl();
    }
    $publicFile = (isset($publicFile[0]) && $publicFile[0] == '/') ? $publicFile : '/' . $publicFile;
    return $rootPathUrl . $publicFile;
}

/**
 * 返回常量内容
 * @param string $key
 * @param string $className
 * @param string $typeName
 * @return null
 * @throws ReflectionException
 */
function constOut(string $key, string $className = '', string $typeName = 'name')
{
    return utils\ReflectionUtils::getInfoByConstsVal($className, $key, $typeName);
}

/**
 * 返回orderStatus的常量内容
 * @param string $key
 * @param string $typeName
 * @return null
 * @throws ReflectionException
 */
function constOutOrderStatus(string $key, string $typeName = 'name'){
    return constOut($key, \app\common\consts\OrderStatus::class, $typeName);
}




























