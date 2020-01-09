<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 18:17
 */

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\model\StoreOrderDetailsModel;

class StoreOrderDetailsMapper extends BaseMapper{
    use InstanceTrait;

    /**
     * 通过便利店订单号获取便利店订单详情
     * @param $StoreOrderNo
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getStoreOrderDetail($StoreOrderNo){
        return StoreOrderDetailsModel::where('store_order_no',$StoreOrderNo)->select();
    }
}