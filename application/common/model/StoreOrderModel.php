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

class StoreOrderModel extends BaseModel
{
    protected $name = "StoreOrderMaster";

    protected $formatStatusFields = [
        //便利店订单评价状态
        'evaluation_status' => ['key'=>'evaluation_status_name', 'method'=>OrderEvaluationStatus::class, 'type'=>ModelFormatFieldType::CONST, 'value'=>'name'],
        //便利店订单状态
        'status' => ['key'=>'status_name', 'method'=>OrderStatus::class, 'type'=>ModelFormatFieldType::CONST, 'value'=>'name'],
    ];

    /**
     * 订单详情
     * @return \think\model\relation\HasMany
     */
    public function details()
    {
        return $this->hasMany('StoreOrderDetailsModel','store_order_no','store_order_no')
            ->field("order_no,order_id,store_order_no,store_order_id,goods_sku,goods_brand_name,goods_name,qty,del_status,logo_pic");
    }
}