<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 15:42
 */
namespace app\common\mapper;

use app\common\consts\OrderStatus;
use app\common\model\StoreOrderModel;
use traits\think\Instance;

class StoreOrderMapper extends BaseMapper{

    use Instance;

    /**通过order_no获取便利店订单信息
     * @param $orderNo
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getStoreOrderInfo($orderNo){
        return StoreOrderModel::where($this->createEffectiveWhere(['order_no' => $orderNo]))->select();
    }

    /**通过order_no获取便利店订单数量
     * @param $orderNo
     * @return int
     */
    public function storeOrdeCount($orderNo){
        return StoreOrderModel::where($this->createEffectiveWhere(['order_no' => $orderNo]))->count();
    }

    /**
     * 更新子订单评价状态by store_order_no
     * @param $storeOrderNo
     * @param $storeOrderEvaluation
     * @return $this
     */
    public function updateStoreEvaluationStatus($storeOrderNo,$storeOrderEvaluation){
        return StoreOrderModel::where($this->createEffectiveWhere(['store_order_no' => $storeOrderNo]))->update(['evaluation_status'=>$storeOrderEvaluation]);
    }

    /**
     * 获得用户订单数量
     * @param int $userId
     * @param int $plateformId
     * @param array $map
     * @return int
     */
    public function getStoreOrderQty(int $userId, int $plateformId, array $map = [])
    {
        $condition = $this->createWhere($userId, $plateformId, $map);
        $qty = StoreOrderModel::where($condition)->count();
        return intval($qty) > 0 ? intval($qty) : 0;
    }

    /**
     * 构建查询条件
     * @param int $userId
     * @param int $plateformId
     * @param array $map
     * @return array
     */
    protected function createWhere(int $userId, int $plateformId, array $map)
    {
        return $this->createEffectiveWhere(
            array_merge($map, [
                'user_id' => $userId,
                'plateform_id' => $plateformId
            ])
        );
    }

    /**
     * 获取用户的订单列表
     * @param int $userId
     * @param int $plateformId
     * @param array $map
     * @param int $page
     * @param int $pageSize
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreOrderList(int $userId, int $plateformId, array $map = [], int $page, int $pageSize)
    {
        $condition = $this->createWhere($userId, $plateformId, $map);

        $order = [
            'created_at' => 'desc',
        ];
        $field = "id,store_order_no,order_no,status,plateform_id,goods_qty,created_at,del_status,evaluation_status";
        $list = StoreOrderModel::where($condition)->field($field)->order($order)->page($page)->limit($pageSize)->select();
        foreach ($list as $value){
            $value->details;
        }
        return $list;
    }

    /**
     * 便利店确认接单
     * @param int $userId
     * @param int $plateformId
     * @param string $storeOrderNo
     * @return bool
     */
    public function storeConfirmOrder(int $userId, int $plateformId, string $storeOrderNo){
        $condition = $this->createWhere($userId, $plateformId, ['store_order_no' => $storeOrderNo]);
        $result = StoreOrderModel::where($condition)->update(['status' => OrderStatus::USER_SURE]);
        return $result > 0 ? true : false;
    }

    /**
     * 便利店取消订单
     * @param int $userId
     * @param int $plateformId
     * @param string $storeOrderNo
     * @return bool
     */
    public function storeCancelOrder(int $userId, int $plateformId, string $storeOrderNo){
        $condition = $this->createWhere($userId, $plateformId, ['store_order_no' => $storeOrderNo]);
        $result = StoreOrderModel::where($condition)->update(['status' => OrderStatus::USER_CANCEL]);
        return $result > 0 ? true : false;
    }

}