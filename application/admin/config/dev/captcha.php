<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/12
 * Time: 8:53
 */

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 验证码字符集合
    'codeSet'  => '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',
    // 验证码字体大小(px)
    'fontSize' => 28,
    // 是否画混淆曲线
    'useCurve' => false,
    //是否添加杂点
    'useNoise' => true,
    // 验证码图片高度
    'imageH'   => 0,
    // 验证码图片宽度
    'imageW'   => 0,
    // 验证码位数
    'length'   => 4,
    // 验证成功后是否重置
    'reset'    => true,
    //过期时间S
    'expire'   => 300,
    //是否开启中文验证码
    'useZh'    => false,
    //中文验证码字符集
    'zhSet'    => '',
];