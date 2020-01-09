<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 11:08
 */

namespace app\index\controller;


use app\common\service\OrderService;
use app\common\service\StoreOrderService;
use think\Db;

class Order extends Common
{

    /**
     * @var OrderService
     */
    private $orderService;

    /**
     * @var StoreOrderService
     */
    private $storeOrderService;

    /**
     * 实例化后调用方法
     * @return mixed
     */
    protected function _after_instance()
    {
//        //验证参数
//        $this->checkPost();
        $this->orderService = OrderService::instance();

        $this->storeOrderService = StoreOrderService::instance();
    }


    /**
     * 获取用户订单列表
     * @return array
     */
    public function getUserList()
    {
        try {
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $page = $this->getPage();
            $pageSize = $this->getPageSize();

            $result = $this->orderService->getUserOrderListPage($userId, $plateformId, [], $page, $pageSize);
            return $this->indexResp->ok($result)->send();
        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }

    }

    /**
     * 创建订单
     * @return mixed
     */
    public function createOrder()
    {
        try {
            Db::startTrans();
            $userId = $this->getUserId();
            $userName = $this->user->getNickName();
            $plateformId = $this->getPlateformId();
            $goods  = $this->getPostInputData('goods',[]);
            $rec_info  = $this->getPostInputData('rec_info',[]);
            $address  = [$rec_info['provinceId'], $rec_info['cityId'], $rec_info['areaId'], $rec_info['address']];
            $remark = $this->getPostInputData('remark','','trim');
            $order = $this->orderService->getOrderPrepare($goods, $address, $plateformId, $remark);
            if(empty($goods) || empty($rec_info) || empty($order) )
            {
                throw new \Exception("传参数据异常,有为空项！");
            }

            $result = $this->orderService->createOrder($userId,  $userName, $plateformId ,$goods ,$order ,$rec_info);
            if (!$result){
                return $this->indexResp->err()->send();
            }
            Db::commit();
            return $this->indexResp->ok(['tips'=>"创建订单成功！"])->send();
        } catch (\Exception $e) {
            Db::rollback();
            return $this->indexResp->err(['tips'=>"创建订单失败！","error"=>$this->catchExcpetion($e)])->send();
        }
    }

    /**
     * 获取订单相关价格
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderPrepare()
    {
        try{
            $plateformId = $this->getPlateformId();
            $goods  = $this->getPostInputData('goods',[]);
            $address  = $this->getPostInputData('address',[]);
            $remark = $this->getPostInputData('remark','','trim');
            $res = $this->orderService->getOrderPrepare($goods ,$address ,$plateformId, $remark);
            return $this->indexResp->ok(['prepareInfo'=>$res])->send();
        }catch (\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 通过订单号搜索订单详情
     * @return array
     */
    public function searchOrderByNo(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $orderNo = $this->getPostInputData('orderNo', '', 'trim');

            $info = $this->orderService->searchOrderByNo($userId, $plateformId, $orderNo);
            return $this->indexResp->ok($info)->send();
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 获取订单评价状态
     * @return mixed
     */
    public function getOrderEvaluationStatus()
    {
        try{
            $orderNo = $this->getPostInputData('order_no');
            if (empty($orderNo)) {
                return $this->indexResp->err('请传递有效order_no')->send();
            }
            $evaluationStatus = $this->orderService->getOrderEvaluationStatus($orderNo);
            return $this->indexResp->ok($evaluationStatus)->send();
        }catch (\Exception $e){
            return $this->catchExcpetion($e);
        }
    }


    /**
     * 订单取消
     * @return array
     */
    public function orderCancel(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $orderNo = $this->getPostInputData('orderNo', '', 'trim');

            $result = $this->orderService->orderCancel($userId, $plateformId, $orderNo);
            if ($result){
                return $this->indexResp->ok()->send();
            }else{
                return $this->indexResp->err("取消订单出错")->send();
            }
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 获取便利店订单与详情
     * @return mixed
     */
    public function getStoreOrderInfo(){
        try{
            $orderNo = $this->getPostInputData('order_no');
            if (empty($orderNo)) {
                return $this->indexResp->err('请传递有效order_no')->send();
            }
            $evaluationStatus = $this->storeOrderService->getStoreOrderInfo($orderNo);
            return $this->indexResp->ok($evaluationStatus)->send();
        }catch (\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 查询订单日志（对外）
     * @return array
     */
    public function getOutOrderLog(){
        try{
            $orderNo = $this->getPostInputData('orderNo', '', 'trim');
            $storeOrderNo = $this->getPostInputData('storeOrderNo', '', 'trim');
            $result = $this->orderService->getOutOrderLog($orderNo, $storeOrderNo);
            return $this->indexResp->ok($result)->send();
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 查询对内订单日志
     * @return array
     */
    public function getInOrderLog(){
        try{
            $orderNo = $this->getPostInputData('orderNo', '', 'trim');
            $storeOrderNo = $this->getPostInputData('storeOrderNo', '', 'trim');
            $result = $this->orderService->getInOrderLog($orderNo, $storeOrderNo);
            return $this->indexResp->ok($result)->send();
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 确认收货
     * @return array
     */
    public function confirmReceipt(){
        try{
            $userId = $this->getUserId();
            $userName = $this->user->getNickName();
            $plateformId = $this->getPlateformId();
            $orderNo = $this->getPostInputData('orderNo', '', 'trim');

            $result = $this->orderService->confirmReceipt($userId, $userName, $plateformId, $orderNo);
            if ($result){
                return $this->indexResp->ok()->send();
            }else{
                return $this->indexResp->err("确认收货出错")->send();
            }
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 确认订单
     * @return array
     */
    public function confirmOrder(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $userName = $this->user->getNickName();
            $orderNo = $this->getPostInputData('orderNo', '', 'trim');

            $result = $this->orderService->confirmOrder($userId, $userName, $plateformId, $orderNo);
            if($result){
                return $this->indexResp->ok()->send();
            }else{
                return $this->indexResp->err('确认订单出错')->send();
            }
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }
}