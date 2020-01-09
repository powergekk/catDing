<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/15
 * Time: 14:29
 */

namespace app\common\service;


use app\common\base\traits\InstanceTrait;
use app\common\consts\OrderAllotmentType;
use app\common\consts\OrderLogType;
use app\common\consts\OrderStatus;
use app\common\consts\TableStatus;
use app\common\consts\YesOrNo;
use app\common\mapper\GoodsMapper;
use app\common\mapper\OrderMapper;
use app\common\model\GoodsModel;
use app\common\model\PlateformModel;
use utils\PageUtils;


class OrderService extends ServiceAbstract
{
    use InstanceTrait;


    /**
     * @var OrderMapper
     */
    private $orderMapper;

    /**
     * @var GoodsMapper
     */
    private $goodsMapper;


    /**
     *
     * @var OrderLogService
     */
    private $orderLogService;

    /**
     * @var $OrderDetailsMapper
     */

    /**
     * 实例化
     */
    protected function _after_instance()
    {
        $this->orderMapper = OrderMapper::instance();
        $this->goodsMapper = GoodsMapper::instance();
        $this->orderLogService = OrderLogService::instance();
    }


    /**
     * 获取用户的订单列表页面
     * @param int $userId
     * @param int $plateformId
     * @param array $map
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserOrderListPage(int $userId, int $plateformId, array $map = [], int $page, int $pageSize)
    {
        $total = $this->orderMapper->getUserOrderQty($userId, $plateformId, $map);
        $list = [];
        if (PageUtils::hasItems($page, $pageSize, $total)) {
            $list = $this->orderMapper->getUserOrderList($userId, $plateformId, $map, $page, $pageSize);
        }
        return PageUtils::formatPageResult($page, $pageSize, $total, $list);
    }

    /**
     * 创建订单
     * @param int $userId
     * @param string $userName
     * @param int $plateformId
     * @param array $goods
     * @param array $order
     * @param array $rec_info
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function createOrder(int $userId, string $userName, int $plateformId, array $goods, array $order, array $rec_info)
    {
        //ordermaster新增订单
        $orderNo = $this->createOrderNo($plateformId);
        $plateform  = PlateformModel::where('id',$plateformId)->find();
        $masterInsert = $this->getMasterData($userId, $userName , $plateform ,$order ,$rec_info, $orderNo);
        $order_id = $this->orderMapper->createMaster($masterInsert);

        //订单商品新增
        foreach ($goods as $value){
            $goodInfo = $this->goodsMapper->getGoodsInfoBySku($value['sku'], $plateformId, '*');
            $detailsInsert = $this->getDetailsData($orderNo ,$order_id , $userId, $userName, $plateform ,$goodInfo, $value);
            $result = $this->orderMapper->createDetails($detailsInsert);
            if (empty($result)){
                return false;
            }
        }
        //TODO rizhi
        $this->orderLogService->createOrderLog($orderNo, '', $userId, $userName, OrderLogType::ORDER_CREATE);

        return true;
    }

    /**
     * 组装master数据
     * @param int $userId
     * @param string $userName
     * @param $plateform
     * @param $order
     * @param $rec_info
     * @param $orderNo
     * @return array
     */
    public function getMasterData(int $userId, string $userName ,$plateform, $order, $rec_info, $orderNo)
    {

        $data = [];
        $data['user_id'] = $userId;
        $data['order_no'] = $orderNo;
        $data['plateform_no'] = $plateform->plateform_no;
        $data['plateform_name'] = $plateform->name;
        $data['plateform_order_status'] = "wait";
        $data['plateform_user_no'] = $plateform->plateform_no; //待定
        $data['status'] = OrderStatus::NEW;
        $data['goods_qty'] = $order['goodsQty'];//f
        $data['rec_name'] = $rec_info['recName'];
        $data['rec_tel'] = $rec_info['recTel'];
        $data['rec_province_name'] = $rec_info['provinceName'];
        $data['rec_province_id'] = $rec_info['provinceId'];
        $data['rec_city_name'] = $rec_info['cityName'];
        $data['rec_city_id'] = $rec_info['cityId'];
        $data['rec_area_name'] = $rec_info['rec_area_name'];
        $data['rec_area_id'] = $rec_info['areaId'];
        $data['rec_address'] = $rec_info['address'];
        $data['remark'] = $order['remark'];
        $data['invoice_type'] = 1;
        $data['invoice_header'] = "";
        $data['invoice_status'] = 0;
        $data['invoice_amount'] = 0;
        $data['invoice_bank'] = "";
        $data['invoice_bank_code'] = "";
        $data['invoice_rec_tel'] = "";
        $data['invoice_rec_name'] = "";
        $data['invoice_address'] = "";
        $data['goods_amount'] = $order['goods_amount'];
        $data['order_amount'] = $order['orderAmount'];
        $data['ship_amount'] = $order['shipAmount'];
        $data['accept_order_time'] = "1970-01-01 00:00:00";
        $data['accept_order_user_id'] = 0;
        $data['accept_order_user_name'] = "";
        $data['delivery_order_time'] = "1970-01-01 00:00:00";
        $data['rec_order_time'] = "1970-01-01 00:00:00";
        $data['created_user_id'] = $userId;
        $data['created_user_name'] = $userName;
        $data['updated_user_id'] = 0;
        $data['updated_user_name'] = '';
        $data['del_status'] = YesOrNo::NO;
        $data['allotment_status'] = TableStatus::NEW;
        $data['allotment_type'] = OrderAllotmentType::STORE;
        return $data;
    }

    /**
     * 组装orderdetails数组
     * @param $orderNo
     * @param $orderId
     * @param int $userId
     * @param string $userName
     * @param $plateForm
     * @param $goodInfo
     * @param $goods
     * @return array
     */
    public function getDetailsData($orderNo, $orderId, int $userId, string $userName, $plateForm, $goodInfo, $goods)
    {
        $details = [];
        $details['order_no'] = $orderNo;
        $details['order_id'] = $orderId;
        $details['logo_pic'] = $goodInfo->logo_pic;
        $details['goods_sku'] = $goodInfo->sku;
        $details['goods_name'] = $goodInfo->name;
        $details['goods_category_id'] = $goodInfo->category_id;
        $details['goods_category_name'] = $goodInfo->category_name;
        $details['goods_brand_id'] = $goodInfo->brand_id;
        $details['goods_brand_name'] = $goodInfo->brand_name;
        $details['mark_price'] = $goodInfo->market_price;
        $details['sale_price'] = $goods['saleprice'];
        $details['third_price'] = $goodInfo->third_price;
        $details['zhpt_price'] = 0;
        $details['qty'] = $goods['qty'];
        $details['created_user_id'] = $userId;
        $details['created_user_name'] = $userName;
        $details['updated_user_id'] = 0;
        $details['updated_user_name'] = '';
        $details['del_status'] = YesOrNo::NO;
        return $details;
    }

    /**
     * 生成订单号
     * @param $plateformid
     * @return string
     */
    public function  createOrderNo($plateformId)
    {
        //开头changsha Quick Cat
        $orderStart = "CSQC0".$plateformId;
        $orderMain = date('YmdHis') . rand(100,999);

        return $orderStart.$orderMain;

    }

    /**
     * 计算订单价格详情
     * @param array $goods
     * @param array $address
     * @param int $plateformid
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderPrepare(array $goods ,array $address ,int $plateformid, string $remark)
    {
        $goodstotal = 0;
        $goodsQty = 0;
        foreach ($goods as $goodsitem  => $goodsval){
              $sku[] = $goodsval['sku'];
              $qtys[$goodsval['sku']] = $goodsval['qty'];
        }

        $goodsList = [];
        $goods = $this->getOrderGoodsInfo($sku ,$plateformid);
        foreach ($goods as $skuitem => $skuval){
            //商品计算
            $price = $skuval->sale_price;
            $qty = $qtys[$skuval->sku];
            $goodsQty +=$qty ;
            $skutotal = $price*$qty;
            $goodstotal += $skutotal;

            //出参商品数组
            $goodsList[$skuitem]['sku'] = $skuval->sku;
            $goodsList[$skuitem]['name'] = $skuval->name;
            $goodsList[$skuitem]['goodsPic'] = $skuval->logo_pic;
            $goodsList[$skuitem]['sku_price_total'] = floatval($skutotal);
            $goodsList[$skuitem]['goods_price'] = floatval($price);
            $goodsList[$skuitem]['qty'] = $qty;
        }

        //订单运费计算
        if(!empty($address)){
            $shipping  = 0;
        }

        $order = [];
        $order['remark'] = $remark;
        $order['goods_amount'] = $goodstotal;
        $order['orderAmount'] = $goodstotal+$shipping;
        $order['shipAmount'] = $shipping;
        $order['goodsQty'] = $goodsQty;
        $order['goodList'] = $goodsList;

        return $order;
    }

    /**
     * 获取预下单商品数据
     * @param array $sku
     * @param $plateformid
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getOrderGoodsInfo(array $sku, int $plateformid)
    {
        $skus = implode(',',$sku);
        return GoodsModel::where(['sku'=>array('in',$skus),'third_plateform_id'=>$plateformid ,'del_status'=>YesOrNo::NO])
                            ->field("sku,name,logo_pic,sale_price")->select();
    }

    /**
     * 通过订单编号搜索订单详情
     * @param $userId
     * @param $plateformId
     * @param $orderNo
     * @return array|false|\PDOStatement|string|\think\Model\OrderModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchOrderByNo(int $userId, int $plateformId, string $orderNo){
        return $this->orderMapper->SearchOrderByNo($userId, $plateformId, $orderNo);
    }


    /**
     * 根据订单号获取订单评价状态
     * @param $orderNo
     * @return array|string
     */
    public function getOrderEvaluationStatus($orderNo)
    {
        return $this->orderMapper->getOrderEvaluationStatus($orderNo);
    }

    /**
     * 取消订单
     * @param int $userId
     * @param int $plateformId
     * @param string $orderNo
     * @return bool
     */
    public function orderCancel(int $userId, int $plateformId, string $orderNo){
        return $this->orderMapper->orderCancel($userId, $plateformId, $orderNo);
    }

    /**
     * 确认收货
     * @param int $userId
     * @param int $plateformId
     * @param string $orderNo
     * @return bool
     */
    public function confirmReceipt(int $userId, string $userName, int $plateformId, string $orderNo){
        $result = $this->orderMapper->confirmReceipt($userId, $plateformId, $orderNo);

        $this->orderLogService->createOrderLog($orderNo, '', $userId, $userName, OrderLogType::ORDER_STATUS, "确认收货");

        return $result;
    }

    /**
     * 订单确认
     * @param int $userId
     * @param int $plateformId
     * @param string $orderNo
     * @return bool
     */
    public function confirmOrder(int $userId, string $userName, int $plateformId, string $orderNo){
        $result = $this->orderMapper->confirmOrder($userId, $plateformId, $orderNo);
        $this->orderLogService->createOrderLog($orderNo, '', $userId, $userName, OrderLogType::ORDER_STATUS, "确认订单");

        return $result;
    }

    /**
     * 查询订单对外日志
     * @param string $orderNo
     * @param string $storeOrderNo
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getOutOrderLog(string $orderNo, string $storeOrderNo=''){
        $result = $this->orderMapper->getOutOrderLog($orderNo,$storeOrderNo);
        return $result;
    }

    /**
     * 查询订单对内日志
     * @param string $orderNo
     * @param string $storeOrderNo
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getInOrderLog(string $orderNo, string $storeOrderNo=''){
        $result = $this->orderMapper->getInOrderLog($orderNo,$storeOrderNo);
        return $result;
    }
}