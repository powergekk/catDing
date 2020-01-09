<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/28
 * Time: 19:25
 */

namespace app\index\controller;

use app\common\service\CartService;
use utils\PageUtils;

class Cart extends Common
{

    /**
     * @var CartService
     */
    private $cartService;


    /**
     * 实例化后调用方法
     * @return mixed
     */
    protected function _after_instance()
    {
//        //验证参数
        $this->cartService = CartService::instance();
    }


    /**
     * 获取用户的购物车数量
     * @return array
     */
    public function getQty()
    {
        try {
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $qty = $this->cartService->getCartGoodsQty($userId, $plateformId);
            return $this->indexResp->ok(['qty' => $qty])->send();
        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }
    }


    /**
     * 操作购物车
     * list : {'sku': string, 'qty':int, 'operate':string,  'isCheck':int}
     * operate: add , del , sub, ref
     * @return array
     */
    public function changeGoods()
    {
        try {
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $listGoodsQty = $this->getPostInputData('list');
            if (empty($listGoodsQty) || !is_array($listGoodsQty)) {
                throw new \RuntimeException("参数 list 异常!");
            }
            $flag = $this->cartService->operateGoods($userId, $plateformId, $listGoodsQty);
            if ($flag) {
                return $this->indexResp->ok()->send();
            } else {
                return $this->indexResp->err("操作失败，请稍后重试!")->send();
            }
        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }
    }


    /**
     * 获取用户的购物车列表
     * page: int, pageSize:int
     */
    public function getList()
    {
        try {
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $page = $this->getPage();
            $pageSize = $this->getPageSize();

            $total = $this->cartService->getCartGoodsQty($userId, $plateformId);
            if ($total <= ($page - 1) * $pageSize) {
                $list = [];
            } else {
                $list = $this->cartService->getList($userId, $plateformId, $page, $pageSize);
            }

            $ret = PageUtils::formatPageResult($page, $pageSize, $total, $list);
            //
            return $this->indexResp->ok($ret)->send();
        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }
    }


}