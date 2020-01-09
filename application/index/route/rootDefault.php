<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/12
 * Time: 10:28
 */

/**
 * 这个写当前模块的路由
 */

return [
//    '__pattern__' => [
//        'name' => '\w+',
//    ],
//    '[hello]'     => [
//        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//        ':name' => ['index/hello', ['method' => 'post']],
//    ],
//
////    '[user]'    => [
////        'address' => []
////    ],
    '/' => 'index/Index/index',
    '/index' => 'index/Index/index',
    '' => 'index/Index/index',

];