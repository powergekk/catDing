<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 15:53
 */

namespace app\store\controller;


use app\common\service\GoodsService;
use app\common\service\StoreGoodsService;
use utils\PageUtils;

class StoreGoods extends Common
{
    /**
     * @var StoreGoodsService
     */
    private $storeGoodsService;

    /**
     * @var GoodsService
     */
    private $goodsService;

    /**
     * 实例化
     * @return mixed|void
     */
    protected function _after_instance()
    {
        $this->storeGoodsService = StoreGoodsService::instance();
        $this->goodsService = GoodsService::instance();
    }


    /**
     * 获取用户便利店商品列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserGoodsList(){
        try{
            return $this->getList();
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**获取上架商品列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUpGoodsList(){
        try{
            $map['up_down_status'] = 'up';
            return $this->getList($map);
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 获取下架商品列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDownGoodsList(){
        try{
            $map['up_down_status'] = "down";
            return $this->getList($map);
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 获取便利店商品列表方法
     * @param array $map
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getList(array $map = []){
        $page = $this->getPage();
        $pageSize = $this->getPageSize();
        $userId = $this->getUserId();
        $plateformId = $this->getPlateformId();

        $userGoodsList = $this->storeGoodsService->getUserGoodsList($userId, $plateformId, $map, $page, $pageSize);
        $count = count($userGoodsList);

        $ret = PageUtils::formatPageResult($page, $pageSize, $count, $userGoodsList);
        return $this->indexResp->ok($ret)->send();
    }

    /**
     * 查询可以添加的商品
     * @return array
     */
    public function getGoodsToAdd(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $page = $this->getPage();
            $pageSize = $this->getPageSize();
            $ret = $this->storeGoodsService->getAddGoods($userId, $plateformId, $page, $pageSize);
            return $this->indexResp->ok($ret)->send();
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 添加商品到便利店
     * @return array
     */
    public function addGoods(){
        try{
            $userId = $this->getUserId();
            $userName = $this->user->getNickName();
            $plateformId = $this->getPlateformId();
            $goods = $this->getPostInputData('goods', []);
            $result = $this->storeGoodsService->addGoods($userId, $userName, $plateformId, $goods);

            if ($result){
                return $this->indexResp->ok()->send();
            }else{
                return $this->indexResp->err('添加商品出现错误')->send();
            }
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }


    /**
     * 批量下架商品
     * @return array
     */
    public function getStoreGoodsDown(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            //upGoodsSku,downGoodsSku为上下架商品sku组成的数组
            $upGoodsSku = $this->getPostInputData('upGoodsSku', []);

            //将上架商品下架
            $result = $this->storeGoodsService->getGoodsDown($userId, $plateformId, $upGoodsSku);
            if ($result){
                return $this->indexResp->ok()->send();
            }else{
                return $this->indexResp->err("将商品下架出错")->send();
            }
        }catch (\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 批量上架商品
     * @return array
     */
    public function getStoreGoodsUp(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            //upGoodsSku,downGoodsSku为上下架商品sku组成的数组
            $downGoodsSku = $this->getPostInputData('downGoodsSku', []);

            //将上架商品下架
            $result = $this->storeGoodsService->getGoodsUp($userId, $plateformId, $downGoodsSku);
            if ($result){
                return $this->indexResp->ok()->send();
            }else{
                return $this->indexResp->err("将商品上架出错")->send();
            }
        }catch (\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 修改商品供货价
     * @return array
     */
    public function changePrice(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $sku = $this->getPostInputData('sku', '', 'trim');
            $newPrice = $this->getPostInputData('newPrice');

            $price = $this->storeGoodsService->getPrice($userId, $plateformId, $sku);
            if ($newPrice == $price){
                return $this->indexResp->err('价格不能和原价格相同')->send();
            }

            $result = $this->storeGoodsService->changePrice($userId, $plateformId, $sku, $newPrice);
            if ($result){
                return $this->indexResp->ok()->send();
            }else{
                return $this->indexResp->err('修改商品供货价出错')->send();
            }
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 修改商品库存
     * @return array
     */
    public function changeQty(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $sku = $this->getPostInputData('sku', '', 'trim');
            $newQty = $this->getPostInputData('newQty');

            $qty = $this->storeGoodsService->getQty($userId, $plateformId, $sku);
            if ($newQty == $qty){
                return $this->indexResp->err('库存不能和原价格相同')->send();
            }

            $result = $this->storeGoodsService->changeQty($userId, $plateformId, $sku, $newQty);
            if ($result){
                return $this->indexResp->ok()->send();
            }else{
                return $this->indexResp->err('修改商品库存出错')->send();
            }
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }
}