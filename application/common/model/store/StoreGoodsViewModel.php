<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 15:40
 */

namespace app\common\model\store;


use app\common\consts\ModelFormatFieldType;
use app\common\consts\OrderStatus;

class StoreGoodsViewModel extends StoreBaseModel
{
    protected $name = "basic_goods";

    protected $formatStatusFields = [
        //便利店订单状态
        'status' => ['key' => 'status_name', 'method' => OrderStatus::class, 'type' => ModelFormatFieldType::CONST, 'value' => 'name'],
    ];





}