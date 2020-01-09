<?php

namespace app\common\mapper;


use app\common\base\traits\InstanceTrait;
use app\common\model\CartModel;

class CartMapper extends BaseMapper
{
    use InstanceTrait;

    /**
     * 获取购物车商品数量
     * @param int $userId
     * @param int $plateformId
     * @return int
     */
    public function getUserGoodsQty(int $userId, int $plateformId): int
    {
        $count = CartModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId
        ])->count();
        return intval($count);
    }


    /**
     * 获取购物车商品
     * @param int $userId
     * @param int $plateformId
     * @param string $goodsSku
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOne(int $userId, int $plateformId, string $goodsSku)
    {
        $cartModel = CartModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'goods_sku' => $goodsSku
        ])->find();
        return $cartModel;
    }


    /**
     * 获取购物车列表
     * @param int $userId
     * @param int $plateformId
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList(int $userId, int $plateformId, int $page = 1, int $pageSize = 20)
    {
        $list = CartModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId
        ])->order('updated_at desc')->page($page)->limit($pageSize)->select();

        if (empty($list)) {
            return [];
        } else {
            return $list;
        }
    }


    protected function createWhereCondition(int $userId, int $plateformId){

    }

}