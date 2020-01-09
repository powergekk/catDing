<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 9:54
 */

namespace app\common\consts;


/**
 * @#name 时间常量
 * Class StringTime
 * @package app\common\consts
 */
class StringTime
{

    /**
     * @#name 0秒/永久
     * @var int
     */
    const _0_S = 0;

    /**
     * @#name 1秒
     * @var int
     */
    const _1_S = 1;

    /**
     * @#name 1分钟
     * @var int
     */
    const _1_MINUTES = 60;


    /**
     * @#name 5分钟
     * @var int
     */
    const _5_MINUTES = 300;


    /**
     * @#name 1小时
     * @var int
     */
    const _1_HOURS = 3600;


    /**
     * @#name 100小时
     * @var int
     */
    const _100_HOURS = 360000;

    /**
     * @#name 1天
     * @var int
     */
    const _1_DAY = 86400;


    /**
     * @#name 100天
     * @var int
     */
    const _100_DAY = 8640000;

    /**
     * @#name 默认缓存时间，30分钟
     * @var int
     */
    const CACHE_TIME = 1800;


    /**
     * @#name session时长:1小时
     * @var int
     */
    const SESSION_TIME = 3600;

    /**
     * @#name 短时长:10秒
     * @var int
     */
    const SHORT_TIME = 10;


    /**
     * @#name 长时长:1天
     * @var int
     */
    const LONG_TIME = self::_1_DAY;


    /**
     * @#name token时长
     * @var int
     */
    const TOKEN_TIME = self::SESSION_TIME;


}