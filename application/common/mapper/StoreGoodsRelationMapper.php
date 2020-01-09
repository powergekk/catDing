<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 15:42
 */

namespace app\common\mapper;


use app\common\base\traits\InstanceTrait;
use app\common\consts\YesOrNo;
use app\common\model\GoodsModel;
use app\common\model\StoreGoodsRelationModel;
use app\common\service\StoreGoodsRelationService;

class StoreGoodsRelationMapper extends BaseMapper
{
    use InstanceTrait;

    /**
     * 获取便利店商品列表
     * @param int $userId
     * @param int $plateformId
     * @param array $map
     * @param array $page
     * @param string $order
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreGoodsList(int $userId, int $plateformId,  array $map, array $page, string $order, string $field)
    {
        $storeGoodsTableAlias = 'store_goods';
        $goodsTableAlias = 'goods';
        $order = $storeGoodsTableAlias.'.'.$order;

        $params['page'] = $page;
        $params['order'] = $order;
        $ps = $this->getParam($params);
        $start = $ps['page']['start'];
        $limit= $ps['page']['limit'];
        $label = $ps['order']['field'];
        $sort = $ps['order']['desc'];

        $joinOn = "{$goodsTableAlias}.third_plateform_id={$storeGoodsTableAlias}.plateform_id and {$storeGoodsTableAlias}.user_id={$userId} and {$goodsTableAlias}.sku={$storeGoodsTableAlias}.goods_sku";
        $condition = $this->createWhere($userId, $plateformId, $map, $storeGoodsTableAlias);
        return StoreGoodsRelationModel::Where($condition)->alias($storeGoodsTableAlias)
            ->join("__BASIC_GOODS__ {$goodsTableAlias}", $joinOn)
            ->field($field)->order($label,$sort)->limit($start,$limit)
            ->select();
        /*echo (new StoreGoodsRelationModel)->getLastSql();
        exit;*/
    }

    public function getStoreGoodsListByParam(int $userId, int $platformId, array $map = [], array $page, string $order = 'created_at,desc', array $field = [])
    {
        $storeGoodsTableAlias = 'store_goods';
        $goodsTableAlias = 'goods';
        $order = $storeGoodsTableAlias.'.'.$order;

        $params['page'] = $page;
        $params['order'] = $order;
        $ps = $this->getParam($params);
        $start = $ps['page']['start'];
        $limit= $ps['page']['limit'];
        $label = $ps['order']['field'];
        $sort = $ps['order']['desc'];

        $joinOn = "{$storeGoodsTableAlias}";

        $storeGoodsRelationAliasA = 'a';
        $goodsAliasB = 'b';

        $goodsTableModel = new GoodsModel();

        StoreGoodsRelationModel::where()->alias($storeGoodsRelationAliasA)
            ->join($goodsTableModel->getTable()." as $goodsAliasB", "", "left")
            ->select();


    }

    /**
     * 参数组装
     * @param array $params
     * @return array
     */
    protected function getParam(array $params){
        $return = [];
        foreach ($params as $key => $val){
            switch ($key){ //condition 没有做处理
                case 'page' :
                    $return['page']['start'] = ($val['page']-1)* $val['pageSize'];
                    $return['page']['limit'] = $val['pageSize'];
                    break;
                case 'order' :
                    list($field,$desc) = explode(',',$val);
                    $return['order']['field'] = $field ;
                    $return['order']['desc'] = strtoupper($desc) ;
                    break;
                default:
                    continue;
            }
        }

        return $return;
    }

    /**
     * 形成查询条件
     * @param int $userId
     * @param int $plateformId
     * @param array $map
     * @param string $alias
     * @param string $goodsAlias
     * @return mixed
     */
    protected function createWhere(int $userId, int $plateformId, array $map, string $alias = '', string $goodsAlias = '')
    {
        if (!empty($alias)) {
            $alias .= '.';
        }
        if(!empty($goodsAlias)){
            $goodsAlias .= '.';
        }

        $condition[$alias . 'user_id'] = $userId;
        $condition[$alias . 'plateform_id'] = $plateformId;
        $condition[$alias . 'del_status'] = YesOrNo::NO;

        //up down
        if(isset($map['up_down_status'])){
            $condition[$alias . 'up_down_status'] = $map['up_down_status'];
        }


        return $condition;

    }

    /**
     * 批量下架商品
     * @param $userId
     * @param $plateformId
     * @param $sku
     * @return bool
     */
    public function getGoodsDown($userId, $plateformId, $sku){
        $condition = [
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'goods_sku' => $sku,
            'del_status' => YesOrNo::NO
        ];
        return StoreGoodsRelationModel::where($condition)->update(['up_down_status' => "down"]) > 0 ? true : false;
    }

    /**
     * 批量上架商品
     * @param $userId
     * @param $plateformId
     * @param $sku
     * @return bool
     */
    public function getGoodsUp($userId, $plateformId, $sku){
        $condition = [
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'goods_sku' => $sku,
            'del_status' => YesOrNo::NO
        ];
        return StoreGoodsRelationModel::where($condition)->update(['up_down_status' => "up"]) > 0 ? true : false;
    }

    /**
     * 查询商品上下架状态
     * @param $userId
     * @param $plateformId
     * @param $sku
     * @return mixed
     */
    public function getUdStatus($userId, $plateformId, $sku){
        $condition = [
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'goods_sku' => $sku,
            'del_status' => YesOrNo::NO
        ];
        return StoreGoodsRelationModel::where($condition)->value('up_down_status');
    }

    /**
     * 查询商品Sku列表
     * @param int $userId
     * @param int $plateformId
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsSkusList(int $userId, int $plateformId){
        return StoreGoodsRelationModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId
        ])->field('goods_sku')->select();
    }

    /**
     * 添加单个商品到便利店
     * @param $data
     * @return bool
     */
    public function addGood($data){
        $storeGoods = new StoreGoodsRelationModel;
        $storeGoods->data($data);
        return $storeGoods->save() == 1 ? true : false;
    }

    /**
     * 修改便利店商品供货价格
     * @param int $userId
     * @param int $plateformId
     * @param string $sku
     * @param float $newPrice
     * @return bool
     */
    public function changePrice(int $userId, int $plateformId, string $sku, float $newPrice){
        $result = StoreGoodsRelationModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'del_status' => YesOrNo::NO,
            'goods_sku' => $sku
        ])->update(['purchase_price' => $newPrice]);

        return $result > 0 ? true : false;
    }

    /**
     * 获取便利店商品供货价格
     * @param int $userId
     * @param int $plateformId
     * @param string $sku
     * @return mixed
     */
    public function getPrice(int $userId, int $plateformId, string $sku){
        return StoreGoodsRelationModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'del_status' => YesOrNo::NO,
            'goods_sku' => $sku
        ])->value('purchase_price');
    }

    /**
     * 修改便利店商品库存数量
     * @param int $userId
     * @param int $plateformId
     * @param string $sku
     * @param int $newQty
     * @return bool
     */
    public function changeQty(int $userId, int $plateformId, string $sku, int $newQty){
        $result = StoreGoodsRelationModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'del_status' => YesOrNo::NO,
            'goods_sku' => $sku
        ])->update(['store_qty' => $newQty]);

        return $result > 0 ? true : false;
    }

    /**
     * 获取便利店商品库存数量
     * @param int $userId
     * @param int $plateformId
     * @param string $sku
     * @return mixed
     */
    public function getQty(int $userId, int $plateformId, string $sku){
        return StoreGoodsRelationModel::where([
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'del_status' => YesOrNo::NO,
            'goods_sku' => $sku
        ])->value('store_qty');
    }

    /**
     * 按条件获取店铺下的商品数量
     * @param int $userId
     * @param int $platformId
     * @param array $mapper
     * @return int|string
     */
    public function getGoodsQty(int $userId, int $platformId, array $mapper = [])
    {
        $count =  StoreGoodsRelationModel::where([
            'user_id' => $userId,
            'plateform_id' => $platformId,
        ])->where($mapper)->count();
//        echo StoreGoodsRelationModel::getLastSql();die;
        return $count;
    }
}