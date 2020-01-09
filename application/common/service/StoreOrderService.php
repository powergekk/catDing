<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 15:36
 */
namespace app\common\service;

use app\common\base\traits\InstanceTrait;
use app\common\mapper\StoreOrderDetailsMapper;
use app\common\mapper\StoreOrderMapper;
use utils\PageUtils;

class StoreOrderService extends ServiceAbstract{
    use InstanceTrait;

    /**
     * @var StoreOrderMapper
     */
    protected $storeOrderMapper;

    /**
     * @var StoreOrderDetailsMapper
     */
    protected $storeOrderDetailMapper;

    public function _after_instance(){
        $this->storeOrderMapper = StoreOrderMapper::instance();
        $this->storeOrderDetailMapper = StoreOrderDetailsMapper::instance();
    }

    /**
     * 通过订单号获取便利店订单信息与详情
     * @param $orderNo
     * @return string
     */
    public function getStoreOrderInfo($orderNo){
        try{
            $storeOrderInfo = $this->storeOrderMapper->getStoreOrderInfo($orderNo);
            if (empty($storeOrderInfo)){
                throw new \Exception('订单号无效');
            }
            foreach ($storeOrderInfo as $key=>$value){
                $storeOrderList[$key] = $value;
                $storeOrderList[$key]['details'] = $this->storeOrderDetailMapper->getStoreOrderDetail($value->store_order_no);
                if (empty($storeOrderList[$key]['details'])){
                    throw new \Exception('便利店（拆分）订单号无效');
                }
            }
            return $storeOrderList;
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * 获取用户的订单列表页面
     * @param int $userId
     * @param int $plateformId
     * @param array $map
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreOrderListPage(int $userId, int $plateformId, array $map = [], int $page, int $pageSize)
    {
        $total = $this->storeOrderMapper->getStoreOrderQty($userId, $plateformId, $map);
        $list = [];
        if (PageUtils::hasItems($page, $pageSize, $total)) {
            $list = $this->storeOrderMapper->getStoreOrderList($userId, $plateformId, $map, $page, $pageSize);
        }
        return PageUtils::formatPageResult($page, $pageSize, $total, $list);
    }

    /**
     * 便利店接单确认
     * @param int $userId
     * @param int $plateformId
     * @param string $storeOrderNo
     * @return bool
     */
    public function storeConfirmOrder(int $userId, int $plateformId, string $storeOrderNo){
        return $this->storeOrderMapper->storeConfirmOrder($userId, $plateformId, $storeOrderNo);
    }

    /**
     * 便利店取消订单
     * @param int $userId
     * @param int $plateformId
     * @param string $storeOrderNo
     * @return bool
     */
    public function storeCancelOrder(int $userId, int $plateformId, string $storeOrderNo){
        return $this->storeOrderMapper->storeCancelOrder($userId, $plateformId, $storeOrderNo);
    }
}