<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4
 * Time: 10:20
 */
namespace app\common\service;

use app\common\mapper\GoodsMapper;
use app\common\service\UserService;
use app\common\service\GoodsService;
use app\common\base\traits\InstanceTrait;
use app\common\model\StoreGoodsRelationModel;
use app\common\mapper\StoreGoodsRelationMapper;


class StoreGoodsRelationService extends ServiceAbstract
{
    use InstanceTrait;

    /**
     * @var StoreGoodsRelationModel
     */
    private $storeGoodsRelationMapper;

    /**
     * @var GoodsService
     */
    private $goodsService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * 实例化后调用的方法,用于注入
     */
    protected function _after_instance()
    {
        $this->storeGoodsRelationMapper = StoreGoodsRelationMapper::instance();
        $this->goodsService = GoodsService::instance();
        $this->userService = UserService::instance();
    }

    public function getList(int $userId, int $platformId, $condition = '', $page = 1, $pageSize = 10)
    {
        $mapperCondition = $this->createMapperCondition($condition);
        $pages['page'] = $page;
        $pages['pageSize'] = $pageSize;
        $field = 'id, goods_sku, plateform_id, user_id, status, up_down_status, purchase_price, store_qty, sale_qty';
        $list = $this->storeGoodsRelationMapper->getStoreGoodsList($userId, $platformId, $mapperCondition, $pages, 'created_at,desc', $field);
        return $list;
    }

    /**
     * 获取条件下的商品数量
     * @param int $userId
     * @param int $platformId
     * @param string $mapper
     * @return mixed
     */
    public function getStoreGoodsQty(int $userId, int $platformId, $mapper = '')
    {
        $mapperCondition = $this->createMapperCondition($mapper);
        return $this->storeGoodsRelationMapper->getGoodsQty($userId, $platformId, $mapperCondition);
    }

    /**
     * 创建mapper用的条件
     * @param $condition
     * @return array
     */
    private function createMapperCondition($condition)
    {
        $mapperCondition = [];
        $goods = $this->goodsService->getGoodsList(['name|spec' => ['LIKE', '%'.$condition.'%']], '', 1, 9999, 'sku, spec');

        $skuField = '';
        if($goods)
        {
            foreach ($goods as $good) {
                $skuField .= ','.$good->sku;
            }
            $mapperCondition['goods_sku'] = [['in', array_unique(explode(',', trim($skuField, ',')))], ['LIKE', '%'.$condition.'%'], 'or'];
        } else
        {
            $mapperCondition['goods_sku'] = ['LIKE', '%'.$condition.'%'];
        }

        return $mapperCondition;
    }
}