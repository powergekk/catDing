<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/8
 * Time: 17:44
 */

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\model\GoodsEvaluationModel;

class GoodsEvaluationMapper extends BaseMapper
{
    use InstanceTrait;

    /**
     * sku获取商品
     * @param string $sku
     * @param int $plateformId
     * @return array|false|null|\PDOStatement|string|\think\Model|GoodsModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsEvaluationsBySku(string $sku,int $plateformId ,string $field = '*',int $start,int $limit){
        $evaluation = GoodsEvaluationModel::where($this->createEffectiveWhere(['goods_sku' => $sku ,'plateform_id'=>$plateformId]))->field($field)->limit($start,$limit)->select();

        if(empty($evaluation)){
            return null;
        }else{
            return $evaluation;
        }
    }

    /**
     * 获取商品评价总数
     * @param array $condition
     * @return int
     */
    public function getGoodsEvaluationCount(array $condition)
    {
        return GoodsEvaluationModel::where($condition)->count();
    }

    /**
     * 新增订单商品评价表数据
     * @param array $date
     * @return $this
     */
    public function saveGoodsEvaluation(array $date){
        return $result = GoodsEvaluationModel::create($date);
    }

}