<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 14:30
 */

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\consts\InOut;
use app\common\consts\OrderEvaluationStatus;
use app\common\consts\OrderStatus;
use app\common\model\OrderLogModel;
use app\common\model\OrderDetailsModel;
use app\common\model\OrderModel;
use app\common\model\PlateformModel;
use app\common\model\TokenModel;
use app\index\base\IndexResp;
use utils\ReflectionUtils;


class OrderMapper extends BaseMapper
{
    use InstanceTrait;

    /**
     * 获取用户的订单列表
     * @param int $userId
     * @param int $plateformId
     * @param array $map 查询条件
     * @param int $page
     * @param int $pageSize
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserOrderList(int $userId, int $plateformId, array $map = [], int $page, int $pageSize)
    {
        $condition = $this->createWhere($userId, $plateformId, $map);
        $order = [
            'created_at' => 'desc',
        ];
        $field = "id,status,order_no,plateform_name,plateform_user_no,plateform_order_status,created_at,evaluation_status";
        $list = OrderModel::where($condition)->field($field)->order($order)->page($page)->limit($pageSize)->select();
        foreach ($list as $value){
            $value->storeOrderNos;
            $value->details;
        }
        return $list;
    }


    /**
     * 获得用户订单数量
     * @param int $userId
     * @param int $plateformId
     * @param array $map
     * @return int
     */
    public function getUserOrderQty(int $userId, int $plateformId, array $map = [])
    {
        $condition = $this->createWhere($userId, $plateformId, $map);
        $qty = OrderModel::where($condition)->count();
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
        $plate = PlateformModel::field('plateform_no')->find($plateformId);
        return $this->createEffectiveWhere(
            array_merge($map, [
                'user_id' => $userId,
                'plateform_no' => $plate->plateform_no
            ])
        );
    }

    /**
     * 添加订单信息
     * @param $masterInsert
     * @return mixed
     */
    public function createMaster($masterInsert){
        $order = new OrderModel;
        $order->data($masterInsert);
        $order->save();
        return $order->id;
    }

    /**
     * 添加订单商品详情
     * @param $detailsInsert
     * @return false|int
     */
    public function createDetails($detailsInsert){
        $details = new OrderDetailsModel;
        $details->data($detailsInsert);
        return $details->save();
    }

    /**
     * 根据订单号搜索订单详情
     * @param $userId
     * @param $plateformId
     * @param $orderNo
     * @return array|false|\PDOStatement|string|\think\Model\OrderModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchOrderByNo($userId, $plateformId, $orderNo){
        $orderInfo = OrderModel::where([
            'user_id' => $userId,
            'plateform_no' => IndexResp::instance()->getPlateformNo(),
            'order_no' => array('like',"%".$orderNo."%")
        ])->field("id,status,order_no,plateform_name,plateform_user_no,plateform_order_status,created_at,evaluation_status")->find();
        $orderInfo->storeOrderNos;
        $orderInfo->details;
        return $orderInfo;
    }

    /**
     * 根据订单号获取订单评价状态
     * @param $orderNo
     * @return array|string
     */
    public function getOrderEvaluationStatus($orderNo)
    {
        try {
            $orderEvaluationStatus = OrderModel::where('order_no', $orderNo)->field('evaluation_status')->find();
            if (empty($orderEvaluationStatus)) {
                throw new \Exception('无效订单！');
            }
            return $orderEvaluationStatus;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 取消订单
     * @param $userId
     * @param $plateformId
     * @param $orderNo
     * @return bool
     */
    public function orderCancel($userId, $plateformId, $orderNo){
        $result = OrderModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'order_no' => $orderNo
        ])->update(['status' => OrderStatus::USER_CANCEL]);
        return $result > 0 ? true : false;
    }

    /**
     * 确认收货
     * @param $userId
     * @param $plateformId
     * @param $orderNo
     * @return bool
     */
    public function confirmReceipt($userId, $plateformId, $orderNo){
        $result = OrderModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'order_no' => $orderNo
        ])->update(['status' => OrderStatus::USER_OK]);
        return $result > 0 ? true : false;
    }

    /**
     * 确认订单
     * @param int $userId
     * @param int $plateformId
     * @param string $orderNo
     * @return bool
     */
    public function confirmOrder(int $userId, int $plateformId, string $orderNo){
        $result = OrderModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'order_no' => $orderNo
        ])->update(['status' => OrderStatus::USER_SURE]);
        return $result > 0 ? true : false;
    }

    /**
     * 更新指定订单评价状态
     * @param $orderNo
     * @param $evaluationStatus
     * @return $this
     */
    public function updateEvaluationStatus($orderNo,$evaluationStatus){
        return OrderModel::where($this->createEffectiveWhere(['order_no' => $orderNo]))->update(['evaluation_status' => $evaluationStatus]);
    }

    /**
     * 查询订单日志(对外)
     * @param string $orderNo
     * @param string $storeOrderNo
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOutOrderLog(string $orderNo, string $storeOrderNo){
        $field = "id,order_no,store_order_no,out_content,show_type,created_at,updated_at,created_user_id,
                created_user_name,updated_user_id,updated_user_name,del_status,log_type";
        if($storeOrderNo === ''){
            $storeOrderNoCondition = [];
        }else{
            $storeOrderNoCondition = ['store_order_no' => ['in', ['', $storeOrderNo]]];
        }
        $condition = $this->createEffectiveWhere(array_merge($storeOrderNoCondition,[
            'order_no' => $orderNo,
            'show_type' => ['in', [InOut::OUT, InOut::BOTH]]
        ]));
        return OrderLogModel::where($condition)->field($field)->order('created_at')->select();
    }

    /**
     * 查询订单日志（对内）
     * @param string $orderNo
     * @param string $storeOrderNo
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInOrderLog(string $orderNo, string $storeOrderNo){
        $field = "id,order_no,store_order_no,in_content,show_type,created_at,updated_at,created_user_id,
                created_user_name,updated_user_id,updated_user_name,del_status,log_type";
        if ($storeOrderNo === ''){
            $storeOrderNoCondition = [];
        }else{
            $storeOrderNoCondition = ['store_order' => ['in', ['', $storeOrderNo]]];
        }
        $condition = $this->createEffectiveWhere(array_merge($storeOrderNoCondition,[
            'order_no' => $orderNo,
            'show_type' => ['in', [InOut::IN, InOut::BOTH]]
        ]));
        return OrderLogModel::where($condition)->field($field)->order('created_at')->select();
    }


}