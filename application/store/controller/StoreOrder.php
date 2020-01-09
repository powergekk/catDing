<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/1
 * Time: 17:31
 */

namespace app\store\controller;


use app\common\service\StoreOrderService;

class StoreOrder extends Common
{
    /**
     * @var storeOrderService;
     */
    private $storeOrderService;

    protected function _after_instance()
    {
        // TODO: Implement _after_instance() method.
        $this->storeOrderService = StoreOrderService::instance();
    }

    /**
     * 获取便利店订单列表（查询功能）
     * @return array
     */
    public function getStoreOrderList()
    {
        try {
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $page = $this->getPage();
            $pageSize = $this->getPageSize();

            $result = $this->storeOrderService->getStoreOrderListPage($userId, $plateformId, [], $page, $pageSize);
            return $this->indexResp->ok($result)->send();
        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 便利店接单
     * @return \app\store\base\IndexResp|array|mixed
     */
    public function storeConfirmOrder(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->user->getPlateformId();
            $storeOrderNo = $this->getPostInputData('storeOrderNo', '', 'trim');

            $result = $this->storeOrderService->storeConfirmOrder($userId, $plateformId, $storeOrderNo);
            if ($result){
                return $this->indexResp->ok()->send();
            }else{
                return $this->indexResp->err("便利店接单重复或出错")->send();
            }
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 便利店取消订单
     * @return array
     */
    public function storeCancelOrder(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->user->getPlateformId();
            $storeOrderNo = $this->getPostInputData('storeOrderNo', '', 'trim');

            $result = $this->storeOrderService->storeCancelOrder($userId, $plateformId, $storeOrderNo);
            if ($result){
                return $this->indexResp->ok()->send();
            }else{
                return $this->indexResp->err("便利店取消订单重复或出错")->send();
            }
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

}