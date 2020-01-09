<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/11
 * Time: 18:02
 */
namespace app\common\service;

use app\common\consts\OrderStatus;
use app\common\mapper\OrderMapper;
use app\common\mapper\PlateformMapper;
use app\common\base\traits\InstanceTrait;
use app\common\mapper\order\OrderDetailMapper;

class OrderDetailService extends ServiceAbstract
{
    use InstanceTrait;

    /**
     * @var OrderMapper
     */
    private $orderMapper;

    /**
     * @var OrderDetailMapper
     */
    private $orderDetailMapper;

    /**
     * @var PlateformMapper
     */
    private $plateformMapper;

    protected function _after_instance()
    {
        $this->orderMapper = OrderMapper::instance();
        $this->orderDetailMapper = OrderDetailMapper::instance();
        $this->plateformMapper = PlateformMapper::instance();
    }

    /**
     * 返回订单状态与前端的对应关系
     * @return array
     */
    public function orderRelation()
    {
        return [
            OrderStatus::NEW => 1, //待接单
            OrderStatus::ADMIN_CANCEL => 2, //已拒绝
            OrderStatus::USER_CANCEL => 2,
            OrderStatus::ADMIN_SURE => 3, //待配送
            OrderStatus::USER_SURE => 3,
            OrderStatus::USER_OK => 4, //已完成
            OrderStatus::ADMIN_DELIVERED => 5 //已发货
        ];
    }
    /**
     * 获取订单列表
     * @param array $data
     * @param int $page
     * @param int $pageSize
     * @return \Exception
     */
    public function getOrderList(array $data = [], $page = 1, $pageSize = 10)
    {
        try {
            $platformId = $data['platformId'];
            $data['plateform_no'] = $this->getPlatformById($platformId)->plateform_no;
            if(empty($data['plateform_no']))
            {
                throw new \Exception('不存在的平台');
            }

            $res = $this->orderDetailMapper->getOrderList($data, $page, $pageSize);
            if($res instanceof \Exception)
                throw new \Exception($res->getMessage());

            if($res['total'] === 0)
            {
                return $res;
            } else
            {
                $result = [];
                foreach ($res['data'] as $item)
                {
                    $tmp['orderId'] = $item->id;
                    $tmp['orderNo'] = $item->store_order_no;
                    $tmp['consignee'] = $item->rec_name;
                    $tmp['address'] = $item->rec_provice_name . $item->rec_city_name . $item->rec_area_name . $item->rec_address;
                    $tmp['phone'] = $item->rec_tel;
                    $tmp['orderTotal'] = floatval($item->order_amount);
                    $tmp['createdAt'] = $item->created_at;
                    $tmp['orderStatus'] = isset($this->orderRelation()[$item->status])? $this->orderRelation()[$item->status] : 0;
                    if(in_array($item->status, array_keys($this->orderRelation())))
                        $tmp['statusName'] = constOutOrderStatus($item->status, 'order');
                    else
                        $tmp['statusName'] = '';
                    $result[] = $tmp;
                }
            }
            $res['data'] = $result;

            return $res;
        } catch (\Exception $exception)
        {
            return $exception;
        }

    }

    /**
     * 按条件只获取订单数据
     * @param array $condition
     * @param string $field
     * @return array|false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderByCondition(array $condition = [], string $field = '*')
    {
        return $this->orderDetailMapper->getOrderByCondition($condition, $field);
    }

    /**
     * 根据平台id获取平台信息
     * @param int $platformId
     * @return \app\common\model\PlateformModel|array|\Exception|false|\PDOStatement|string|\think\Model
     */
    public function getPlatformById(int $platformId)
    {
        try {
            $platformInfo = $this->plateformMapper->getById($platformId);
            if(!$platformInfo)
                throw new \Exception('平台不存在');
            return $platformInfo;
        } catch (\Exception $exception)
        {
            return $exception;
        }
    }

    /**
     * 按条件获取订单和商品信息
     * @param array $condition
     * @return \Exception|array
     */
    public function getOrderGoodsByCondition(array $condition = [])
    {
        try {
            $platformId = $condition['platformId'];
            $condition['plateform_no'] = $this->getPlatformById($platformId)->plateform_no;

            if(empty($condition['plateform_no']))
            {
                throw new \Exception('不存在的平台');
            }

            $res = $this->orderDetailMapper->getOrderGoodsByCondition($condition);

            if(!empty($res))
            {
                $orderInfo['orderId'] = $res[0]->order_id;
                $orderInfo['orderNo'] = $res[0]->order_no;
                $orderInfo['consignee'] = $res[0]->rec_name;
                $orderInfo['address'] = $res[0]->rec_provice_name . $res[0]->rec_city_name . $res[0]->rec_area_name . $res[0]->rec_address;
                $orderInfo['phone'] = $res[0]->rec_tel;
                $orderInfo['orderTotal'] = floatval($res[0]->order_amount);
                $orderInfo['createdAt'] = $res[0]->created_at;
                $orderInfo['orderStatus'] = isset($this->orderRelation()[$res[0]->status])? $this->orderRelation()[$res[0]->status] : 0;
                $orderInfo['statusName'] = constOutOrderStatus($res[0]->status, 'order');
                foreach ($res as $item)
                {
                    $goodsInfo['goodsId'] = $item->sku_id;
                    $goodsInfo['sku'] = $item->goods_sku;
                    $goodsInfo['goodsName'] = $item->goods_name;
                    $goodsInfo['goodsImage'] = $item->logo_pic;
                    $goodsInfo['goodsAmount'] = intval($item->qty);
                    $goodsInfo['goodsSpec'] = $item->goods_spec;
                    $goodsInfo['category'] = $item->goods_category_name;
                    $goodsInfo['units'] = $item->goods_units;
                    $goodsInfo['goodsPrice'] = floatval($item->sale_price);
                    $orderInfo['goodsList'][] = $goodsInfo;
                }
            } else
            {
                throw new \Exception('该订单没有商品');
            }

            return $orderInfo;
        } catch (\Exception $exception)
        {
            return $exception;
        }
    }

    /**
     * 操作订单状态
     * @param int $userId
     * @param string $userName
     * @param array $condition
     * @return \Exception|false|int
     */
    public function editOrder(int $userId, string $userName, array $condition = [])
    {
        try {
            //先检查是否存在order
            $where = [
                'id' => $condition['orderId'],
                'plateform_no' => $this->getPlatformById($condition['platformId'])->plateform_no,
                'store_no' => $condition['store_no']
            ];

            $exist = $this->getOrderByCondition($where);
            if(empty($exist))
                throw new \Exception('该店铺不存在的订单');

            if($exist[0]->status === OrderStatus::USER_OK)
                throw new \Exception('该订单已完成，不能操作');

            $operationTyppe = [
                1 => OrderStatus::ADMIN_SURE,
                2 => OrderStatus::ADMIN_CANCEL,
                3 => OrderStatus::ADMIN_DELIVERED,
                4 => OrderStatus::USER_OK
            ];
            if($condition['type'] === 1)
            {
                if($exist[0]->status !== OrderStatus::NEW)
                    throw new \Exception('[' . constOutOrderStatus($exist[0]->status, 'order') . ']状态的订单不能做[接单]操作');

            } elseif ($condition['type'] === 2)
            {
                if($exist[0]->status !== OrderStatus::NEW)
                    throw new \Exception('[' . constOutOrderStatus($exist[0]->status, 'order') . ']状态的订单不能做[拒绝]操作');
            } elseif ($condition['type'] === 3)
            {
                if($exist[0]->status !== OrderStatus::ADMIN_SURE && $exist[0]->status !== OrderStatus::USER_SURE)
                    throw new \Exception('[' . constOutOrderStatus($exist[0]->status, 'order') . ']状态的订单不能做[发货]操作');
            }

            $where = [
                'id' => $condition['orderId'],
            ];

            $param = [
                'status' => $operationTyppe[$condition['type']],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_user_id' => $userId,
                'updated_user_name' => $userName
            ];

            $res = $this->orderDetailMapper->editOrder($where, $param);

            if($res === false)
                throw new \Exception('操作失败');
            return $res;

        } catch (\Exception $exception)
        {
            return $exception;
        }
    }

    /**
     * 获取评价列表
     * @param array $condition
     * @param int $page
     * @param int $pageSize
     * @return \Exception
     */
    public function getEvaList(array $condition = [], $page = 1, $pageSize = 10)
    {
        try {
            $data['store_no'] = $condition['storeNo'];
            $data['plateform_no'] = $this->getPlatformById($condition['platformId'])->plateform_no;

            if(empty($data['plateform_no']))
            {
                throw new \Exception('不存在的平台');
            }


            if($condition['type'] === 5) //查看全部，需要分页
            {
                $res = $this->orderDetailMapper->getEvaList($data, true, $page, $pageSize);
                $data['days'] = 0;
            } else
            {
                $condition['type'] === 4 && $data['days'] = 30;
                $condition['type'] === 3 && $data['days'] = 15;
                $condition['type'] === 2 && $data['days'] = 7;
                $condition['type'] === 1 && $data['days'] = 1;
                $res = $this->orderDetailMapper->getEvaList($data, false);
            }
            if($res instanceof \Exception)
                throw new \Exception($res->getMessage());

            $timeArr = [];
            for ($day = 1; $day <= $data['days']; $day++)
            {
                $timeArr[] = date('Y-m-d', strtotime('-' . $day . 'day'));
            }

            $result = [];

            if(!empty($res['data']) && $condition['type'] === 5)
            {
                foreach ($res['data'] as $item)
                {
                    $tmp['time'] = $item->dateTime? $item->dateTime : '';
                    $tmp['orderNumber'] = intval($item->orderTotal); //订单总数
                    $tmp['orderTotal'] = floatval($item->orderPrice); //商品总价
                    if($tmp['orderNumber'] == 0)
                        $tmp['gradeNumber'] = 0;
                    else
                        $tmp['gradeNumber'] = floatval($item->scoreTotal / $tmp['orderNumber']); //评价
                    $result[] = $tmp;
                }
            } elseif(empty($res['data']) && $condition['type'] !== 5)
            {
                foreach ($timeArr as $item)
                {
                    $tmp['time'] = $item;
                    $tmp['orderNumber'] = 0;
                    $tmp['orderTotal'] = 0;
                    $tmp['gradeNumber'] = 0;
                    $result[] = $tmp;
                }
            } elseif(!empty($res['data']) && $condition['type'] !== 5)
            {
                $res['data'] = array_column($res['data'], null, 'dateTime');
                foreach ($timeArr as $item)
                {
                    if(isset($res['data'][$item]))
                    {
                        $tmp['time'] = $item;
                        $tmp['orderNumber'] = intval($res['data'][$item]['orderTotal']);
                        $tmp['orderTotal'] = floatval($res['data'][$item]['orderPrice']);
                        $tmp['gradeNumber'] = floatval($res['data'][$item]['scoreTotal'] / $tmp['orderNumber']);
                        $result[] = $tmp;
                    }
                }
            }
            $res['data'] = $result;

            return $res;
        } catch (\Exception $exception)
        {
            return $exception;
        }

    }

}