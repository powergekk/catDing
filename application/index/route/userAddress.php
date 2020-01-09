<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/12
 * Time: 20:55
 */
return [
    //获取用户地址列表
    'list' => 'index/UserAddress/getUserAddressList',
    //新增/修改地址信息
    'set' => 'index/UserAddress/saveUserAddress',
    //设定默认
    'set/default' => 'index/UserAddress/setDefault',
    //删除用户地址
    'delete' => 'index/UserAddress/del',
    //获取用户默认(或最新)地址
    'get/default' => 'index/UserAddress/getDefaultInfo',
    //获取用户具体地址信息
    'get/one' => 'index/UserAddress/getInfo',

];