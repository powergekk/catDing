<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/12
 * Time: 8:53
 */

// +----------------------------------------------------------------------
// | 会话设置
// +----------------------------------------------------------------------

return [
    'id' => '',
    // SESSION_ID的提交变量,解决flash上传跨域
    'var_session_id' => '',
    // SESSION 前缀
    'prefix' => 'think',
    // 驱动方式 支持redis memcache memcached
//    'type' => '',
    'type' => 'Redis',
    'host' => '192.168.10.25',
    'port' => '6379',
    'select' => 3,
    // 是否自动开启 SESSION
    'auto_start' => true,
];