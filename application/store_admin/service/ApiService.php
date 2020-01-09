<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/29
 * Time: 15:57
 */

namespace app\store_admin\service;


class ApiService extends BaseService
{

    //必须带上
    protected static $instance;



    public function index(User $user){
        $user->ss();
    }


}