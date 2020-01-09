<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
//var_export(getRouteParams([BIND_MODULE]));exit;
return getRouteParams([BIND_MODULE]);


/**
 * 获取指定模块的路由
 * @param string $module
 * @param string $rootDefault
 * @return mixed
 */
function getModuleRouteParams(string $module = 'index', string $rootDefault = 'rootDefault.php')
{
    $rootPath = __DIR__ . DS . $module . DS . 'route';
    $files = scandir($rootPath);


    $rel = [];
    if (is_file($rootPath . DS . $rootDefault)) {
        $rel = array_merge($rel, require $rootPath . DS . $rootDefault);
    }

//获取模块路由
    foreach ($files as $file) {
        if (is_dir($rootPath . DS . $file) || $file === '.' || $file === '..' || $file === $rootDefault) {
            continue;
        }
        $routeTmp = require $rootPath . DS . $file;
        foreach ($routeTmp as $r => $v) {
            if (empty($v)) {
                continue;
            }
            $name = str_replace(".php", "", $file);
            //转下横线
            $_name = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $name);
            $rel[str_replace("_", "/", strtolower($_name)) . '/' . $r] = $v;
        }
    }
    return $rel;
}

/**
 * 获取路由
 * @param array $modules
 * @return array
 */
function getRouteParams(array $modules)
{
    $rel = [];
    foreach ($modules as $m) {
        $rel = array_merge($rel, getModuleRouteParams($m));
    }
    return $rel;
}
