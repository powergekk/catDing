<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 17:27
 */

namespace app\common\service;


use app\common\base\traits\InstanceTrait;
use app\common\mapper\GoodsMapper;
use app\common\model\GoodsModel;

class GoodsService extends ServiceAbstract
{
    use InstanceTrait;
    /**
     * @var GoodsMapper
     */
    private $goodsMapper;


    /**
     *
     */
    public function _after_instance(){
        $this->goodsMapper = GoodsMapper::instance();
    }

    /**
     * @todo 获取商品列表
     * @param $condition array 筛选条件
     * @param $order string 排序
     * @param $pagenum int 分页数
     * @param $field string
     * @return object
     */
    public function getGoodsList(array $condition,string $order='created_at,DESC' ,int $pagenum = 1,int $pagesize,string $field='*'): array
    {
        $pages['page'] = $pagenum;
        $pages['pageSize'] = $pagesize;
        $list = $this->goodsMapper->getGoodsList($condition,$order,$pages,$field);

        if(empty($list)){
            return array();
        }
        return $list;
    }

    /**
     * 获取分类字符串
     * @param array $params
     * @return string
     */
    public function getParamsString(array $params){
        return implode(',', $params);
    }

    /*
     * @todo 查询商品详情
     *  @param string condition
     * @param int $plateformId
     */
    public function getGoodsDetails(array  $condition,int $palteformid ,$field ="*" )
    {
        if($condition['field'] == 'sku'){
            return $this->getGoodsInfoBySku($condition['keywords'],$palteformid ,$field);
        }else{
           return $this->getGoodsInfoById($condition['keywords'],$palteformid ,$field);
        }
    }

    /**
     * 通过SKU查询商品
     * @param string $sku
     * @param int $plateformId
     * @return GoodsModel|array|false|null|\PDOStatement|string|\think\Model|GoodsModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsInfoBySku(string $sku, int $plateformId,string $field="*")
    {
        $goods = $this->goodsMapper->getGoodsInfoBySku($sku, $plateformId,$field);
        if(empty($goods)){
            return null;
        }else{
            return $goods;
        }
    }
    /**
     * 通过id查询商品
     * @param string $sku
     * @param int $plateformId
     * @return GoodsModel|array|false|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsInfoById(string $sku, int $plateformId,string $field="*")
    {
        $goods = $this->goodsMapper->getGoodsInfoById($sku, $plateformId,$field);
        if(empty($goods)){
            return null;
        }else{
            return $goods;
        }
    }

    /**
     * 通过SKU查询商品数量
     * @param string $sku
     * @param int $plateformId
     * @return GoodsModel|array|false|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsCount(array $condition )
    {
        return $this->goodsMapper->getGoodsCount($condition);

    }

    /*
     * @todo 通过sku获取商品销量
     * @param string $sku
     * @param int $plateformid
     * @return mix
     */
    public function getGoodsSaleTotal(string $sku ,int $plateformid)
    {
        return $this->goodsMapper->getSaleCount($sku ,$plateformid);
    }

    /*
   * @todo 通过sku获取商品好评率
   * @param string $sku
   * @param int $plateformid
   * @return mix
   */
    public function getGoodEvaCount(string $sku ,int $plateformid)
    {
        return $this->goodsMapper->getGoodEvaCount($sku ,$plateformid);
    }
}