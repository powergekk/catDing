<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/11
 * Time: 15:37
 */
namespace app\store\controller;

use utils\PageUtils;
use app\common\service\OrderDetailService;

class Order extends Common
{
    /**
     * @var OrderDetailService
     */
    private $orderDetailService;

    public function _after_instance()
    {
        // TODO: Implement _after_instance() method.
        $this->orderDetailService = OrderDetailService::instance();
    }

    /**
     * 订单列表
     * @return array
     */
    public function list()
    {
        $data['infoContents'] = $this->request->param('data.infoContents'); //收货人信息检索内容
        $data['goodsContents'] = $this->request->param('data.goodsContents'); //商品信息检索内容

        try {
            $page = $this->getPage();
            $pageSize = $this->getPageSize();
            $data['platformId'] = $this->getPlateformId();
            $data['store_no'] = $this->getStoreUserSession()->getStoreInfo()->getStoreNo();
            if(empty($data['store_no']) || empty($data['platformId']))
            {
                throw new \Exception('该用户非法');
            }
            $res = $this->orderDetailService->getOrderList($data, $page, $pageSize);
            if($res instanceof \Exception)
                throw new \Exception($res->getMessage());

            $ret = PageUtils::formatPageResult($page, $pageSize, $res['total'], $res['data']);
            return $this->indexResp->ok($ret, '操作成功')->send();
        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }
    }

    /**
     * 订单详情查看
     * @return array
     */
    public function detail()
    {
        $data['orderId'] = $this->request->param('data.orderId/d', 0); //订单id
        $data['orderNo'] = $this->request->param('data.orderNo'); //订单No

        try {
            if(empty($data['orderId']))
                throw new \Exception('orderId必传');

            $data['platformId'] = $this->getPlateformId();
            $data['store_no'] = $this->getStoreUserSession()->getStoreInfo()->getStoreNo();
            if(empty($data['store_no']) || empty($data['platformId']))
            {
                throw new \Exception('该用户非法');
            }
            $res = $this->orderDetailService->getOrderGoodsByCondition($data);
            if($res instanceof \Exception)
                throw new \Exception($res->getMessage());

            return $this->indexResp->ok($res, '操作成功')->send();
        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }
    }

    /**
     * 订单操作
     * @return array
     */
    public function operate()
    {
        $data['orderId'] = $this->request->param('data.orderId/d', 0);
        $data['type'] = $this->request->param('data.type/d', 0); //操作类型，1.接单 2.拒绝 3.发货 4.完成

        try {
            $data['platformId'] = $this->getPlateformId();
            $data['store_no'] = $this->getStoreUserSession()->getStoreInfo()->getStoreNo();
            if(empty($data['store_no']) || empty($data['platformId']))
            {
                throw new \Exception('该用户非法');
            }

            $res = $this->orderDetailService->editOrder($this->getUserId(), $this->getUserSession()->getNickName(), $data);
            if($res instanceof \Exception)
                throw new \Exception($res->getMessage());

            return $this->indexResp->ok(null, '操作成功')->send();
        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }
    }
}