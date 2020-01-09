<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/8
 * Time: 17:48
 */

namespace app\common\service;

use app\common\base\traits\InstanceTrait;
use app\common\mapper\GoodsEvaluationMapper;
use app\common\mapper\GoodsMapper;
use app\common\mapper\OrderMapper;
use app\common\mapper\StoreOrderEvaluationMapper;
use app\common\mapper\StoreOrderMapper;
use app\common\model\GoodsModel;
use Mockery\Exception;

class GoodsEvaluationService
{
    use InstanceTrait;
    /**
     * @var GoodsEvaluationMapper
     */
    private $goodsEvaluationMapper;

    /**
     * @var StoreOrderEvaluationMapper
     */
    private $storeOrderEvaluationMapper;

    /**
     * @var $goodsService
     */
    private $goodsService;

    /**
     * @var OrderMapper;
     */
    private $orderMapper;

    /**
     * @var StoreOrderMapper;
     */
    private $storeOrderMapper;

//    public function __construct(){
//        $this->goodsEvaluationMapper =GoodsEvaluationMapper::instance();
//        $this->storeOrderEvaluationMapper = StoreOrderEvaluationMapper::instance();
//    }


    /**
     *
     */
    protected function _after_instance()
    {
        $this->goodsEvaluationMapper =GoodsEvaluationMapper::instance();
        $this->storeOrderEvaluationMapper = StoreOrderEvaluationMapper::instance();
        $this->goodsService = GoodsService::instance();
        $this->orderMapper = OrderMapper::instance();
        $this->storeOrderMapper = StoreOrderMapper::instance();
    }


    /**
     * 通过SKU查询商品评价
     * @param string $sku
     * @param string $field
     * @param array $page
     * @return
     */
    public function getGoodsEvaluations(string $sku,int $plateformid = 1  ,array $page ,string $field = '*')
    {
        $start = ($page['page']-1)*$page['pageSize'];
        $limit = $page['pageSize'];
        $evaluation= $this->goodsEvaluationMapper->getGoodsEvaluationsBySku($sku , $plateformid ,$field , $start ,$limit);
        if(empty($evaluation)){
            return array();
        }else{
            return $evaluation;
        }
    }

    /**
     * 查询商品评价总数
     * @param string $sku
     * @param int $plateformId
     * @return GoodsModel|array|false|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodEvaluationsCount(array $condition )
    {
        return $this->goodsEvaluationMapper->getGoodsEvaluationCount($condition);

    }

    /**
     * 新增评价
     * @param $userId
     * @param $userName
     * @param $plateformId
     * @param $goodsList
     * @param $order
     * @return array
     */
    public function saveEvaluations($userId,$userName,$plateformId,$goodsList,$order){
        try{
            db()->startTrans();
            foreach ($goodsList as $k=>$v){
                $date = $v;
                $date['order_no'] = $order['order_no'];
                $date['store_order_no'] = $order['store_order_no'];
                $date['user_id'] = $userId;
                $date['user_name'] = $userName;
                $goodsName = $this->goodsService->getGoodsInfoBySku($v['goods_sku'],$plateformId)->name;
                $date['goods_name'] = $goodsName;
//            $date['created_at'] = $list;
//            $date['updated_at'] = $list;
                $date['created_user_id'] = $userId;
                $date['created_user_name'] = $userName;
                $date['updated_user_id'] = $userId;
                $date['plateform_id'] = $plateformId;
                $this->goodsEvaluationMapper->saveGoodsEvaluation($date);
            }
            $orderDate = $order;
            $orderDate['user_id'] = $userId;
            $orderDate['user_name'] = $userName;
            $orderDate['created_user_id'] = $userId;
            $orderDate['created_user_name'] = $userName;
            $orderDate['updated_user_id'] = $userId;
            $orderDate['level'] = "";
            $orderDate['all_score'] = floor(($orderDate['goods_score']+$orderDate['service_score']+$orderDate['speed_score'])/3);
            if ($this->storeOrderEvaluationMapper->getStoreOrderEvaluation($orderDate['store_order_no'])){
                throw new \Exception('请勿重复评价此订单！');
            }
            $this->storeOrderEvaluationMapper->saveStoreOrderEvaluation($orderDate);
            //子订单修改评价状态
            $storeOrderEvaluation = 'all';
            if(!$this->storeOrderMapper->updateStoreEvaluationStatus($order['store_order_no'],$storeOrderEvaluation)){
                throw new \Exception('更新失败！');
            }
            // 主订单修改评价状态
            $contstoreOrder = $this->storeOrderMapper->storeOrdeCount($order['order_no']);
            $contstoreOrderEvaluation = $this->storeOrderEvaluationMapper->storeOrderEvaluationCount($order['order_no']);
            if ($contstoreOrderEvaluation<$contstoreOrder){
                $evaluationStatus = 'part';
            }else{
                $evaluationStatus = 'all';
            }
            if(!$this->orderMapper->updateEvaluationStatus($order['order_no'],$evaluationStatus)){
                throw new \Exception('更新失败！');
            }


            db()->commit();
            $success = true;
            $message = "数据新增成功！";
        }catch (\Exception $e){
            db()->rollback();
            $success = false;
            $message = $e->getMessage();
        }
        $result = array('success'=>$success,'message'=>$message);
        return $result;
    }

}