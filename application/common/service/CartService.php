<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/28
 * Time: 17:56
 */

namespace app\common\service;


use app\common\base\traits\InstanceTrait;
use app\common\cache\ComCache;
use app\common\consts\GoodsUpDown;
use app\common\consts\YesOrNo;
use app\common\mapper\CartMapper;
use app\common\model\CartModel;
use think\Config;

class CartService extends ServiceAbstract
{
    use InstanceTrait;

    /**
     * @var CartMapper
     */
    protected $cartMapper;


    /**
     * @var GoodsService
     */
    protected $goodsService;


    /**
     * @var UserService
     */
    protected $userService;


    /**
     * 实例化后调用的方法,用于注入
     */
    protected function _after_instance()
    {
        $this->cartMapper = CartMapper::instance();
        $this->goodsService = GoodsService::instance();
        $this->userService = UserService::instance();

    }


    /**
     * 组装用户的缓存KEY
     * @param int $userId
     * @param int $plateformId
     * @return string
     */
    protected function getCacheKey(int $userId, int $plateformId): string
    {
        return "user:cart:" . $plateformId . ":" . $userId;
    }

    /**
     * 返回用户的购物车商品数
     * @param int $userId
     * @param int $plateformId
     * @return int
     */
    public function getCartGoodsQty(int $userId, int $plateformId): int
    {
        $key = $this->getCacheKey($userId, $plateformId);
        if (ComCache::has($key)) {
            return intval(ComCache::get($key));
        } else {
            return $this->refreshCacheQty($userId, $plateformId);
        }
    }


    /**
     * 设定用户的购物车商品数量
     * @param int $userId
     * @param int $plateformId
     * @param int $qty
     * @return bool
     */
    protected function setCacheQty(int $userId, int $plateformId, int $qty): bool
    {
        $key = $this->getCacheKey($userId, $plateformId);
        return ComCache::set($key, $qty, Config::get('cart_time'));
    }

    /**
     * 强制刷新购物车缓存商品数量
     * @param int $userId
     * @param int $plateformId
     * @return int
     */
    public function refreshCacheQty(int $userId, int $plateformId)
    {
        $count = $this->cartMapper->getUserGoodsQty($userId, $plateformId);
        $this->setCacheQty($userId, $plateformId, $count);
        return $count;
    }


    /**
     * 更新购物车的商品
     * @param int $userId
     * @param int $plateformId
     * @param int $qty
     * @param string $goodsSku
     * @param string $goodsName
     * @param float $price
     * @param int $isCheck 是否勾选(0:未勾选，1:勾选)
     * @param string $logoPic
     * @param int $doUserId
     * @param string $doUserName
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function changeCartGoods(int $userId, int $plateformId, int $qty, string $goodsSku, string $goodsName, float $price, int $isCheck, string $logoPic, int $doUserId, string $doUserName)//: bool
    {
        $cartModel = $this->cartMapper->getOne($userId, $plateformId, $goodsSku);
        if (empty($cartModel)) {
            $cartModel = new CartModel();
            $cartModel->user_id = $userId;
            $cartModel->goods_sku = $goodsSku;
            $cartModel->plateform_id = $plateformId;

            $cartModel->created_at = date('Y-m-d H:i:s');
            $cartModel->created_user_id = $doUserId;
            $cartModel->created_user_name = $doUserName;
        }

        $cartModel->updated_at = date('Y-m-d H:i:s');
        $cartModel->updated_user_id = $doUserId;
        $cartModel->updated_user_name = $doUserName;
        $cartModel->is_check = $isCheck;
        $cartModel->logo_pic = $logoPic;

        $cartModel->qty = $qty > 1 ? $qty : 1;
        $cartModel->goods_name = $goodsName;
        $cartModel->sale_price = $price;
        $cartModel->save();

        return true;

    }


    /**
     * 删除商品
     * @param int $userId
     * @param int $plateformId
     * @param string $goodsSku
     * @return int|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function delCartGoods(int $userId, int $plateformId, string $goodsSku)//: bool
    {
        $cartModel = $this->cartMapper->getOne($userId, $plateformId, $goodsSku);
        if (empty($cartModel)) {
            return null;
        } else {
            return $cartModel->delete();
        }
    }


    /**
     * 获取商品
     * @param int $userId
     * @param int $plateformId
     * @param string $goodsSku
     * @return int|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCartGoods(int $userId, int $plateformId, string $goodsSku)
    {
        return $this->cartMapper->getOne($userId, $plateformId, $goodsSku);
    }


    /**
     * 操作商品
     * @param int $userId
     * @param int $plateformId
     * @param array $listGoodsQty : list  {'sku': string, 'qty':int, 'operate':string, 'isCheck':int}
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function operateGoods(int $userId, int $plateformId, array $listGoodsQty)
    {
        $user = $this->userService->getUserById($userId);
        $doUserId = $user->id;
        $doUserName = $user->nick_name;
        $userName = $user->nick_name;
        foreach ($listGoodsQty as $goodsQty) {
            $sku = $goodsQty['sku'];
            $qty = $goodsQty['qty'];
            $isCheck = isset($goodsQty['isCheck']) ? intval($goodsQty['isCheck']) : 0;
            $operate = $goodsQty['operate'];//add , del , sub, ref
            switch ($operate) {
                case 'add':
                case 'sub':
                case 'ref':
                    //TODO 方法占用
                    $goodsModel = $this->goodsService->getGoodsInfoBySku($sku, $plateformId, $this->getGoodsField());
                    $goodsName = $goodsModel->name;
                    $price = floatval($goodsModel->sale_price);
                    $logoPic = $goodsModel->logo_pic;

                    //
                    $cartGoodsModel = $this->getCartGoods($userId, $plateformId, $sku);
                    if (empty($cartGoodsModel)) {
                        $cartGoodsQty = 0;
                    } else {
                        $cartGoodsQty = intval($cartGoodsModel->qty);
                    }
                    //
                    if ($operate == 'add') {
                        $newQty = $cartGoodsQty + $qty;
                    } elseif ($operate == 'sub') {
                        $newQty = $cartGoodsQty - $qty;
                    } else {
                        $newQty = $qty;
                    }
                    $this->changeCartGoods($userId, $plateformId, $newQty, $sku, $goodsName, $price, $isCheck, $logoPic, $doUserId, $doUserName);

                    break;

                case 'del':
                    $this->delCartGoods($userId, $plateformId, $sku);
                    break;

                default:
                    //TODO逻辑处理

                    break;

            }
        }
        //刷新缓存中的数量
        $this->refreshCacheQty($userId, $plateformId);
        return true;
    }


    /**
     * 获取购物车列表
     * @param int $userId 用户ID
     * @param int $plateformId 平台ID
     * @param int $page 当前页码
     * @param int $pageSize 每页显示记录数
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList(int $userId, int $plateformId, int $page = 1, int $pageSize = 20)
    {
        $list = $this->cartMapper->getList($userId, $plateformId, $page, $pageSize);

        if (empty($list)) {
            $list = [];
        } else {
            foreach ($list as &$cartGoods) {
                $sku = $cartGoods->goods_sku;
                $goods = $this->goodsService->getGoodsInfoBySku($sku, $plateformId, $this->getGoodsField());
                if (empty($goods)) {
                    $cartGoods->del_status = YesOrNo::YES;
                    $cartGoods->status = GoodsUpDown::DOWN;
                    $cartGoods->original_price = $cartGoods->sale_price;//原价格
                } else {
                    $cartGoods->goods_name = $goods->name;
                    $cartGoods->original_price = $goods->original_price;//原价格
                    $cartGoods->sale_price = $goods->sale_price;
                    $cartGoods->status = $goods->isEffective() ? GoodsUpDown::UP : GoodsUpDown::DOWN;
                    $cartGoods->logo_pic = $goods->logo_pic;
                }
            }

        }
        return $list;
    }


    /**
     * 用作查询商品时的内容字段
     * @return string
     */
    protected function getGoodsField()
    {
        return "`name`,`sale_price`,`logo_pic`,`original_price`,`status`,`zhpt_status`,`third_status`";
    }
}