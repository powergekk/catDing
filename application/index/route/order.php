<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 14:28
 */

return [
    'list' => 'index/Order/getUserList',
    'prepare'=>'index/Order/OrderPrepare',
    'create' => 'index/Order/createOrder',
    'search' => 'index/Order/searchOrderByNo',
    'evaluation/status'=>'index/Order/getOrderEvaluationStatus',
    'cancel' => 'index/Order/orderCancel',
    'confirm' => 'index/Order/confirmOrder',
    'rec/confirm' => 'index/Order/confirmReceipt',
    'out/log' => 'index/Order/getOutOrderLog',
    'in/log' => 'index/Order/getInOrderLog',
];