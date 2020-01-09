<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 15:40
 */
namespace app\common\model;


use app\common\consts\ModelFormatFieldType;
use app\common\consts\OrderEvaluationStatus;
use app\common\consts\OrderStatus;

class StoreInfoModel extends BaseModel
{
    protected $name = "StoreInfo";

    protected $formatStatusFields = [
        //便利店订单状态
        'status' => ['key'=>'status_name', 'method'=>OrderStatus::class, 'type'=>ModelFormatFieldType::CONST, 'value'=>'name'],
    ];


}