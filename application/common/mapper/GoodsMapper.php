<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 15:58
 */

namespace app\common\mapper;


use app\common\base\traits\InstanceTrait;
use app\common\consts\DelStatus;
use app\common\consts\YesOrNo;
use app\common\model\GoodsEvaluationModel;
use app\common\model\GoodsModel;
use app\common\model\OrderDetailsModel;

class GoodsMapper  extends BaseMapper
{
    use InstanceTrait;

    /**
     * 获取商品列表
     * @param array $condition
     * @param string $order
     * @param array $page
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsList( array $condition, string $order, array $page, string $field)
    {
        $params['page'] = $page;
        $params['order'] = $order;
        $ps = $this->getParam($params);
        $start = $ps['page']['start'];
        $limit= $ps['page']['limit'];
        $label = isset($ps['order']['field'])? $ps['order']['field']:'';
        $sort = isset($ps['order']['desc'])? $ps['order']['desc']:'';

        return GoodsModel::where($condition)->field($field)->order($label,$sort)->limit($start,$limit)->select();
    }

    public function createViewTable(array $condition = [])
    {
        return GoodsModel::where($condition);
    }

    /**
     * 按条件获取分页的商品
     * @param array $condition
     * @param int $page
     * @param int $pageSize
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsByCondition(array $condition = [], int $page = 1, int $pageSize = 10)
    {
        return $this->createViewTable($condition)
            ->page($page)
            ->limit($pageSize)
            ->select();
    }

    /**
     * 按条件获取商品总数量
     * @param array $condition
     * @return int|string
     */
    public function countByCondition(array $condition = [])
    {
        return $this->createViewTable($condition)
            ->count();
    }

    /**
     * 新增或修改goods表商品
     * @param array $condition
     * @return false|int
     */
    public function addOrEditGoods(array $condition)
    {
        $goodsModel = new GoodsModel();

        if(isset($condition['id'])){
            $where['id'] = $condition['id'];
            if(is_array($condition['id']))
            {
                $where['id'] = ['in', $condition['id']];
            }
            unset($condition['id']);
            $goodsModel->isUpdate(true);
            $res = $goodsModel->save($condition, $where);
        } else {
            $goodsModel->isUpdate(false);
            $res = $goodsModel->save($condition);
        }
//        echo $goodsModel->getLastSql();die;
        return $res;
    }


    /**
     * 参数组装
     * @param $params
     * @return array
     */
    public function getParam($params)
    {
        $return = [];
        foreach ($params as $key => $val){
            switch ($key){ //condition 没有做处理
                case 'page' :
                    $return['page']['start'] = ($val['page']-1)* $val['pageSize'];
                    $return['page']['limit'] = $val['pageSize'];
                    break;
                case 'order' :
                    if($val)
                    {
                        list($field,$desc) = explode(',',$val);
                        $return['order']['field'] = $field ;
                        $return['order']['desc'] = strtoupper($desc) ;
                    }
                    break;
                default:
                    continue;
            }
        }

       return $return;
    }

    /**
     * sku获取商品
     * @param string $sku
     * @param int $plateformId
     * @return array|false|null|\PDOStatement|string|\think\Model|GoodsModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsInfoBySku(string $sku, int $plateformId,string $field)
    {
        $goodsInfo = GoodsModel::where(['sku' => $sku, 'third_plateform_id' => $plateformId, 'del_status' => YesOrNo::NO])->field($field)->find();

        if(empty($goodsInfo)){
            return null;
        }else{
            return $goodsInfo;
        }
    }

    /**
     * id获取商品
     * @param string $id
     * @param int $plateformId
     * @param string $field
     * @return array|false|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsInfoById(string $id, int $plateformId,string $field)
    {
        $goodsInfo = GoodsModel::where(['id' => $id, 'third_plateform_id' => $plateformId, 'del_status' => YesOrNo::NO])->field($field)->find();
        if(empty($goodsInfo)){
            return null;
        }else{
            return $goodsInfo;
        }
    }

    /**
     * 获取商品总数
     * @param array $condition
     * @return int
     */
    public function getGoodsCount(array $condition)
    {
        return GoodsModel::where($condition)->count();
    }

    /**
     * 获取商品总数
     * @param string $sku
     * @param int $plateform
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getSaleCount(string $sku , int $plateform =1 )
    {
        return OrderDetailsModel::Where(['goods_sku'=>$sku ,'third_plateform_id'=>$plateform])->field("SUM(`qty`) as saleTotal")->select();
    }

    /**
     * 获取好评总数
     * @param string $sku
     * @param int $plateform
     * @return float|int
     */
    public function getGoodEvaCount(string $sku , int $plateform =1 )
    {
         $total = GoodsEvaluationModel::where(['goods_sku'=>$sku ,'plateform_id'=>$plateform])->count();
         $good =  GoodsEvaluationModel::where(['goods_sku'=>$sku ,'plateform_id'=>$plateform,'all_score'=>array('gt',79)])->count();

         return !$good ? 0 :($good/$total)*100;
    }

}