<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/10
 * Time: 16:35
 */

namespace app\common\model;


use app\common\consts\ModelFormatFieldType;
use app\common\consts\OrderEvaluationStatus;
use app\common\consts\OrderStatus;

class OrderModel extends BaseModel
{
    protected  $name= "store_order_master";

    protected $insert = ['created_at'];
    protected $update = ['updated_at'];

//    protected function setCreated_atAttr()
//    {
//        return date('YmdHis');
//    }
//    protected function setUpdated_atAttr()
//    {
//        return date('YmdHis');
//    }


    protected $formatStatusFields = [
        //订单评价状态
        'evaluation_status' => ['key'=>'evaluation_status_name', 'method'=>OrderEvaluationStatus::class, 'type'=>ModelFormatFieldType::CONST, 'value'=>'name'],
        //订单状态
        'status' => ['key'=>'status_name', 'method'=>OrderStatus::class, 'type'=>ModelFormatFieldType::CONST, 'value'=>'name'],
    ];
    /**
     * 订单详情
     * @return \think\model\relation\HasMany
     */
    public function details()
    {
        return $this->hasMany('OrderDetailsModel','order_id','id')
            ->field("order_no,order_id,goods_sku,goods_brand_name,goods_name,qty,del_status,logo_pic");
    }

    /**
     * 关联便利店订单
     * @return \think\model\relation\HasMany
     */
    public function storeOrderNos()
    {
        return $this->hasMany('StoreOrderModel','order_no','order_no')
            ->field("store_order_no");
    }
}