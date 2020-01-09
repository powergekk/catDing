<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 10:58
 */
namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\model\StoreOrderEvaluationModel;

class StoreOrderEvaluationMapper extends BaseMapper{
    use InstanceTrait;

    /**
     * 新增订单商品评价表数据
     * @param array $date
     * @return $this
     */
    public function saveStoreOrderEvaluation(array $date){
        return StoreOrderEvaluationModel::create($date);
    }

    /**
     * 获取评价信息by storeOrderNo
     * @param $storOrderNo
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getStoreOrderEvaluation($storeOrderNo){
        return StoreOrderEvaluationModel::where($this->createEffectiveWhere(['store_order_no' => $storeOrderNo]))->select();
    }

    /**
     * 获取评价数量by storeOrderNo
     * @param $storeOrderNo
     * @return int
     */
    public function storeOrderEvaluationCount($orderNo){
        return StoreOrderEvaluationModel::where($this->createEffectiveWhere(['order_no' => $orderNo]))->count();
    }

}