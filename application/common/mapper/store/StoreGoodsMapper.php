<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4
 * Time: 19:30
 */

namespace app\common\mapper\store;


use app\common\consts\GoodsType;
use app\common\model\GoodsModel;
use app\common\consts\GoodsRelationType;
use app\common\model\GoodsRelationModel;
use app\common\base\traits\InstanceTrait;

class StoreGoodsMapper extends StoreBaseMapper
{
    use InstanceTrait;


    /**
     * 根据店铺号获取商品总数
     * @param array $condition
     * @param int $page
     * @param int $pageSize
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListByStoreNo(array $condition, int $page = 1, int $pageSize = 10)
    {
        $list = $this->createViewTable($condition, [])
            ->page($page)
            ->limit($pageSize)
            ->select();

        return $list;

    }


    /**
     * 根据店铺号获取商品总数
     * @param array $condition
     * @return int|string
     */
    public function countByStoreNo(array $condition)
    {
        return $this->createViewTable($condition)->count();
    }

    /**
     * 根据搜索条件获取商品列表
     * @param array $whereCondition and条件
     * @param array $whereOrCondition or条件
     * @param int $page
     * @param int $pageSize
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsByCondition(array $whereCondition, array $whereOrCondition = [], int $page = 1, int $pageSize = 10)
    {
        $list = $this->createViewTable($whereCondition, $whereOrCondition)
            ->page($page)
            ->limit($pageSize)
            ->order('goods.created_at desc')
            ->select();

        //echo GoodsModel::getLastSql();die;
        return $list;
    }

    /**
     * 根据条件获取商品的总数
     * @param array $whereCondition and条件
     * @param array $whereOrCondition or条件
     * @return int|string
     */
    public function countByCondition(array $whereCondition, array $whereOrCondition = [])
    {
        return $this->createViewTable($whereCondition, $whereOrCondition)->count();
    }

    /**
     * 获取关联的sku
     * @param array $whereCondition
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function relationSku(array $whereCondition)
    {
        $list = $this->createViewTable($whereCondition, [], GoodsRelationType::GOODS)->select();
        //echo GoodsModel::getLastSql();die;
        return $list;
    }

    /**
     * 通过sku获取goods表数据
     * @param array $condition
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsBySku(array $condition)
    {
        $res = $this->createViewTable($condition)->select();
        //echo GoodsRelationModel::getLastSql();die;
        return $res;
    }

    /**
     * 获取模板数据
     * @param array $condition
     * @param array $whereOrCondition
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getModelSKu(array $condition, array $whereOrCondition = [])
    {
        $res = $this->createViewTable($condition, $whereOrCondition)
            ->select();
        //echo GoodsModel::getLastSql();die;
        return $res;
    }

    /**
     * 向relation表中增加数据
     * @param array $param
     * @return false|int
     */
    public function addToGoodsRelation(array $param)
    {
        $goodsRelationModel = new GoodsRelationModel();
        $res = $goodsRelationModel->save($param);
        return $res;
    }

    /**
     * 根据platformNo分页获取商品信息
     * @param array $condition 条件，必须包括platformNo
     * @param array $whereOrCondition
     * @param $page
     * @param $pageSize
     * @param $total bool 是否需要总数
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsByPlatform(array $condition, array $whereOrCondition = [], $page = 1, $pageSize = 10, bool $total = false)
    {
        if($total === true)
        {
            $res['total'] = $this->createViewTable($condition, $whereOrCondition, GoodsRelationType::PLATEFORM)->count();
        }
        $res['data'] = $this->createViewTable($condition, $whereOrCondition, GoodsRelationType::PLATEFORM)
            ->page($page)
            ->limit($pageSize)
            ->select();
//echo GoodsRelationModel::getLastSql();die;
        return $res;
    }

    /**
     * 通过sku获取的获取goods关系下的数据
     * @param array $condition sku数组
     * @param array $whereOrCondition
     * @return \Exception|false|\PDOStatement|string|\think\Collection|array
     */
    public function getGoodsByGoods(array $condition, array $whereOrCondition = [])
    {
        try {
            $res = $this->createViewTable($condition, $whereOrCondition, GoodsRelationType::GOODS)
                ->select();
            return $res;
        } catch (\Exception $exception)
        {
            return $exception;
        }
    }

    /**
     * @param array $whereCondition
     * @param array $whereOrCondition
     * @param string $relationType
     * @return GoodsModel
     */
    protected function createViewTable(array $whereCondition, array $whereOrCondition = [], string $relationType = GoodsRelationType::STORE)
    {
        if(($relationType === GoodsRelationType::STORE && (!isset($whereCondition['storeNo']) || !$whereCondition['storeNo'])) ||
            ($relationType === GoodsRelationType::GOODS && (!isset($whereCondition['sku']) || !$whereCondition['sku'])) ||
            ($relationType === GoodsRelationType::PLATEFORM && (!isset($whereCondition['platformNo']) || !$whereCondition['platformNo'])))
            return null;

        $goodsRelationModel = new GoodsRelationModel();

        $onCondition = "goods_relation.sku=goods.sku";

        if($relationType === GoodsRelationType::STORE)
        {
            $relationValue = $whereCondition['storeNo'];
            unset($whereCondition['storeNo']);
        } elseif ($relationType === GoodsRelationType::GOODS)
        {
            $relationValue = ['in', $whereCondition['sku']];
            unset($whereCondition['sku']);
        } else
        {
            $relationValue = $whereCondition['platformNo'];
            unset($whereCondition['platformNo']);
        }

        $where = [
            "goods_relation." . GoodsRelationType::RELATION_TYPE => $relationType,
            "goods_relation." . GoodsRelationType::RELATION_VALUE => $relationValue,
        ];


        if(!empty($whereCondition))
        {
            $where = array_merge($where, $whereCondition);
        }

        if($whereOrCondition)
        {
            $sql = '';
            if(isset($whereOrCondition['sku']))
            {
                $sql .= "goods." . GoodsType::SKU;
                $tmp = $whereOrCondition['sku'];
            }

            if(isset($whereOrCondition['name']))
            {
                $sql .= "|goods." . GoodsType::NAME;
                $tmp = $whereOrCondition['name'];
            }

            if(isset($whereOrCondition['spec']))
            {
                $sql .= "|goods." . GoodsType::SPEC;
                $tmp = $whereOrCondition['spec'];
            }
            $whereOr = [
                trim($sql, '|') => $tmp
            ];
            $where = array_merge($where, $whereOr);
        }


        $field = [
            '*',
            "goods_relation." . GoodsRelationType::RELATION_VALUE => 'store_no',
            "goods.id"

        ];


        return GoodsModel::where($where)->alias("goods")->field($field)->join($goodsRelationModel->getTable() . " goods_relation", $onCondition, "inner");
    }

}