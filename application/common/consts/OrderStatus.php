<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 16:11
 */

namespace app\common\consts;


/**
 * #{订单状态}
 * Class OrderStatus
 * @package app\common\consts
 */
class OrderStatus
{

    /**
     * @#name 新建
     * @#inName 新建
     * @#order 待接单
     * @var string
     */
    const NEW = 'new';


    /**
     * @#name 已确认
     * @#inName 已确认(用户)
     * @#order 待配送
     * @var string
     */
    const USER_SURE = 'user_sure';


    /**
     * @#name 已确认
     * @#inName 已确认(管理员)
     * @#order 待配送
     * @var string
     */
    const ADMIN_SURE = 'admin_sure';


    /**
     * @#name 处理中
     * @#inName 指派订单
     * @var string
     */
    const SPLITING = 'spliting';


    /**
     * @#name 已取消
     * @#inName 已取消(用户)
     * @#order 已拒绝
     * @var string
     */
    const USER_CANCEL = 'user_cancel';


    /**
     * @#name 已取消
     * @#inName 已取消(管理员)
     * @#order 已拒绝
     * @var string
     */
    const ADMIN_CANCEL = 'admin_cancel';

    /**
     * @#name 确认收货
     * @#inName 确认收货(用户)
     * @#order 已完成
     * @var string
     */
    const USER_OK = 'user_ok';

    /**
     * @#name 确认收货
     * @#inName 确认收货(系统)
     * @var string
     */
    const SYSTEM_OK = 'system_ok';


    /**
     * @#name 确认收货
     * @#inName 确认收货(管理员)
     * @var string
     */
    const ADMIN_OK = 'admin_ok';


    /**
     * @#name 已发货
     * @#inName 已发货(管理员)
     * @#order 已发货
     * @var string
     */
    const ADMIN_DELIVERED = 'admin_delivered';

}