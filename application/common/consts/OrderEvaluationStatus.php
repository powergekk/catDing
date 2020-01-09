<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 16:11
 */

namespace app\common\consts;


/**
 * @#name 订单评价状态
 * Class OrderEvaluationStatus
 * @package app\common\consts
 */
class OrderEvaluationStatus
{

    /**
     * @#name 新建
     * @var string
     */
    const NEW = 'new';



    /**
     * @#name 待评价
     * @var string
     */
    const WAIT = 'wait';


    /**
     * @#name 部分评价
     * @var string
     */
    const PART = 'part';


    /**
     * @#name 完成
     * @var string
     */
    const ALL = 'all';


}