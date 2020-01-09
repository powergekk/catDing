<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/11
 * Time: 17:56
 */

namespace app\common\mapper\order;


use app\common\base\traits\InstanceTrait;
use app\common\model\OrderDetailsModel;
use app\common\model\OrderModel;
use app\common\model\order\OrderEvaluationViewModel;

class OrderDetailMapper extends OrderBaseMapper
{
    use InstanceTrait;

    /**
     * 创建order表与details表的关联模型
     * @param array $where
     * @return OrderModel
     */
    protected function createView(array $where = [])
    {
        $orderDetailsModel = new OrderDetailsModel();
        $onCondition = 'master.id = details.store_order_id';

        return OrderModel::where($where)->alias('master')->join($orderDetailsModel->getTable() . ' details' , $onCondition, 'left');
    }

    /**
     * 创建order表与evaluation表的关联模型
     * @param array $where
     * @return OrderModel
     */
    protected function createOrderEvaluationView(array $where = [])
    {
        $orderEva = new OrderEvaluationViewModel();
        $onCondition = 'master.store_order_no = eva.store_order_no';

        $res = OrderModel::where($where)->alias('master')->join($orderEva->getTable() . ' eva', $onCondition, 'left');
        return $res;
    }

    /**
     * 获取评价列表
     * @param array $condition
     * @param bool $total
     * @param int $page
     * @param int $pageSize
     * @return \Exception
     */
    public function getEvaList(array $condition = [], bool $total = true, int $page = 1, int $pageSize = 10)
    {
        try {
            $where = [
                'store_no' => $condition['store_no'],
                'plateform_no' => $condition['plateform_no']
            ];

            $field = [
//                'master.store_order_no',
                'eva.all_score',
                'count(`master`.`id`) as orderTotal',
                'FROM_UNIXTIME(UNIX_TIMESTAMP(`master`.`created_at`), "%Y-%m-%d") as dateTime',
                'sum(`master`.`order_amount`) as orderPrice',
                'sum(`eva`.`all_score`) as scoreTotal'
            ];
            if($total === true)
            {
                $res['total'] = $this->createOrderEvaluationView($where)
                    ->field($field)
                    ->group('dateTime')
                    ->count();
                if($res['total'] === 0)
                {
                    $res['data'] = [];
                } else
                {
                    $res['data'] = $this->createOrderEvaluationView($where)
                        ->field($field)
                        ->group('dateTime')
                        ->page($page)
                        ->limit($pageSize)
                        ->select();
                }
            } else
            {
                $time = date('Y-m-d', strtotime('-' . $condition['days'] . 'day'));
                $nowTime = date('Y-m-d');
                $where = array_merge($where, ['master.created_at' => ['between', [$time, $nowTime]]]);
                
                $res['data'] = $this->createOrderEvaluationView($where)
                    ->field($field)
                    ->group('dateTime')
                    ->select();
            }

//            echo OrderModel::getLastSql();die;
            return $res;
        } catch (\Exception $exception)
        {
            return $exception;
        }
    }

    /**
     * 分页获取订单信息
     * @param array $condition
     * @param int $page
     * @param int $pageSize
     * @param bool $total
     * @return \Exception
     */
    public function getOrderList(array $condition = [], int $page = 1, int $pageSize = 10, bool $total = true)
    {
        try {
            $where = [];
            if(!empty($condition['infoContents']))
            {
                $where = ['master.order_no|master.rec_name|master.rec_tel|master.rec_provice_name|master.rec_city_name|master.rec_area_name|master.rec_address'
                => ['LIKE', '%'. $condition['infoContents'] . '%']];
            } elseif (!empty($condition['goodsContents']))
            {
                $where = array_merge($where, ['details.goods_sku|details.goods_name|details.goods_spec' => ['LIKE', '%'. $condition['goodsContents'] . '%']]);
            }
            if(empty($where))
            {
                $where = [
                    'master.store_no' => $condition['store_no'],
                    'master.plateform_no' => $condition['plateform_no']
                ];
            } else
            {
                $where = array_merge($where, ['master.store_no' => $condition['store_no'], 'master.plateform_no' => $condition['plateform_no']]);
            }

            $fields = [
                'master.id',
                'master.order_no',
                'master.store_order_no',
                'master.store_no',
                'master.plateform_no',
                'master.rec_name',
                'master.rec_tel',
                'master.rec_provice_name',
                'master.rec_city_name',
                'master.rec_area_name',
                'master.rec_address',
                'master.order_amount',
                'master.status',
                'master.created_at',
                'details.goods_sku'
            ];
            if($total === true)
            {
                $res['total'] = $this->createView($where)
                    ->field($fields)
                    ->group('master.id')
                    ->count();
            }

            if(isset($res['total']) && $res['total'] == 0)
            {
                $res['data'] = [];

            } else
            {
                $res['data'] = $this->createView($where)
                    ->field($fields)
                    ->page($page)
                    ->limit($pageSize)
                    ->order('created_at', 'desc')
                    ->group('master.store_order_no')
                    ->order('master.created_at desc')
                    ->select();
            }

//            echo OrderModel::getLastSql();die;
            return $res;
        } catch (\Exception $exception)
        {
            return $exception;
        }
    }

    /**
     * 按条件获取订单以及对应的商品信息
     * @param array $condition
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderGoodsByCondition(array $condition = [])
    {
        $where = [
            'master.store_no' => $condition['store_no'],
            'master.plateform_no' => $condition['plateform_no']
        ];

        isset($condition['orderId']) && $where = array_merge($where, ['master.id' => $condition['orderId']]);

        $field = [
            'master.id as order_id',
            'master.order_no',
            'master.store_no',
            'master.plateform_no',
            'master.rec_name',
            'master.rec_tel',
            'master.rec_provice_name',
            'master.rec_city_name',
            'master.rec_area_name',
            'master.rec_address',
            'master.order_amount',
            'master.status',
            'master.created_at',
            'details.id as sku_id',
            'details.goods_sku',
            'details.goods_name',
            'details.qty',
            'details.logo_pic',
            'details.goods_category_name',
            'details.goods_units',
            'details.sale_price',
            'details.goods_spec'
        ];

        $res = $this->createView($where)
            ->field($field)
            ->order('created_at', 'desc')
            ->select();

//        echo OrderDetailsModel::getLastSql();die;
        return $res;
    }

    /**
     * 按条件只返回订单数据
     * @param array $condition
     * @param string $field
     * @return false|\PDOStatement|string|\think\Collection|array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderByCondition(array $condition = [], string $field = '*')
    {
        return OrderModel::where($condition)->field($field)->select();
    }

    /**
     * 编辑订单信息
     * @param array $where 条件
     * @param array $param 编辑内容
     * @return false|int
     */
    public function editOrder(array $where, array $param)
    {
        $orderModel = new OrderModel();
        $res = $orderModel->save($param, $where);
        return $res;
    }
}