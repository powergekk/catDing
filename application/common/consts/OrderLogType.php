<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/30
 * Time: 17:15
 */

namespace app\common\consts;


/**
 * Class OrderLogType
 * @package app\common\consts
 */
class OrderLogType
{


    /**
     * @#name 物流
     * @var string
     */
    const LOGISTIC = 'logistic';


    /**
     * @#name 订单创建
     * @var string
     */
    const ORDER_CREATE = 'order-create';

    /**
     * @#name 订单状态变更
     * @var string
     */
    const ORDER_STATUS = 'order-status';


    /**
     * @#name 订单修改
     * @var string
     */
    const ORDER_MODIFY = 'order-modify';

}