<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 16:11
 */

namespace app\common\consts;


/**
 * @#name 订单拆分状态
 * Class OrderAllotmentStatus
 * @package app\common\consts
 */
class OrderAllotmentStatus
{

    /**
     * @#name 新建
     * @var string
     */
    const NEW = 'new';



    /**
     * @#name 待分配/拆分
     * @var string
     */
    const WAIT = 'wait';


    /**
     * @#name 部分分配
     * @var string
     */
    const PART = 'part';


    /**
     * @#name 分配完成
     * @var string
     */
    const ALL = 'all';


}