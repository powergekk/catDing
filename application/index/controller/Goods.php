<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 17:42
 */

namespace app\index\controller;


use app\common\consts\TableStatus;
use app\common\consts\YesOrNo;
use app\common\service\GoodsEvaluationService;
use app\common\service\GoodsService;
use utils\PageUtils;

class Goods extends Common
{


    /**
     * @var GoodsService
     */
    private $goodsService;

    /**
     * @var GoodsEvaluationService
     */
    private $goodsEvaluationService;


    /**
     * 4
     * #{实例化函数的后面一步}
     * 可以这一步做登陆验证
     * @return mixed
     */
    protected function _after_instance()
    {
        $this->goodsService = GoodsService::instance();
        $this->goodsEvaluationService = GoodsEvaluationService::instance();
    }

    /**
     * 首页商品列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goodsList()
    {
        return $this->getList();
    }


    /**
     * 商品详情
     * @params field(字段包括sku ，id )可选择传参
     * @param keywords
     * @return mix
     */
    public function goodsDetails()
    {
        try{
            $field_arr = ['sku', 'goodsid'];
            $third_plateform_id = $this->getPlateformId();
            $field = $this->getPostInputData("field", 'sku');
            $keywords = $this->getPostInputData("keywords", '');


            if (!in_array($field, $field_arr)) {
                return $this->indexResp->err('参数传递错误！')->send();
            }
            $condition['field'] = $field;
            $condition['keywords'] = $keywords;

            $goods = $this->goodsService->getGoodsDetails($condition, $third_plateform_id);
            return $this->indexResp->ok(['goods' => $goods])->send();
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }


    /**
     * 商品用户评价信息
     * /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function goodsEvaluations()
    {
        try{
            $pagenum = $this->getPage();
            $pageSize = $this->getPageSize();
            $palteformid = $this->getPlateformId();
            $sku = $this->getPostInputData("sku", 'sku');


            $page = ['page' => $pagenum, 'pageSize' => $pageSize];
            $evaluations = $this->goodsEvaluationService->getGoodsEvaluations($sku, $palteformid, $page);

            $condition = ['goods_sku' => $sku, 'plateform_id' => $palteformid];
            $count = $this->goodsEvaluationService->getGoodEvaluationsCount($condition);
            $saleCount = $this->goodsService->getGoodsSaleTotal($sku, $palteformid);
            $goodEva = $this->goodsService->getGoodEvaCount($sku, $palteformid);


            $ret = PageUtils::formatPageResult($pagenum, $pageSize, $count, $evaluations);
            $ret['saleTotal'] = intval($saleCount[0]['saleTotal']);
            $ret['goodEva'] = $goodEva;
            return $this->indexResp->ok($ret)->send();
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }

    }

    /**
     * 通过关键字搜索商品
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function keySearch()
    {
        try{
            $keyWord = $this->getPostInputData('keyWord', '');
            $condition['name'] = array('like', '%' . $keyWord . '%');

            return $this->getList($condition);
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 分类搜索  传入cateID
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function cateSearch()
    {
        try{
            $cates = $this->getPostInputData('cates', []);
            $catesString = $this->goodsService->getParamsString($cates);
            if (empty($catesString)) {
                return $this->getList();
            } else {
                $condition['category_id'] = array('in', $catesString);
                return $this->getList($condition);
            }
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 活动商品查询（分为品牌活动和特殊商品活动)
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function advertSearch()
    {
        try{
            $type = $this->getPostInputData('types', null, 'trim');
            $params = $this->getPostInputData('params', []);
            $paramsString = $this->goodsService->getParamsString($params);
            if (empty($paramsString)) {
                return $this->getList();
            } else {
                if ($type == "goods") {
                    $condition['sku'] = array('in', $paramsString);
                    return $this->getList($condition);
                }elseif($type = "brand"){
                    $condition['brand_id'] = array('in', $paramsString);
                    return $this->getList($condition);
                } else {
                    return $this->indexResp->err("活动商品有误");
                }
            }
        }catch(\Exception $e){
            return $this->catchExcpetion($e);
        }
    }


    /**
     * 搜索商品原方法
     * @param array $condition
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getList(array $condition = [])
    {
        $page = $this->getPage();
        $pageSize = $this->getPageSize();
        $third_plateform_id = $this->getPostInputData("plateformId", 1);

        $condition['status'] = "up";
        $condition['del_status'] = YesOrNo::NO;
        $condition['third_plateform_id'] = $third_plateform_id;

        $goodslist = $this->goodsService->getGoodsList($condition, 'created_at,DESC', $page, $pageSize);
        $count = $this->goodsService->getGoodsCount($condition);
        $ret = PageUtils::formatPageResult($page, $pageSize, $count, $goodslist);

        return $this->indexResp->ok($ret)->send();
    }


    /**
     * 商品评价保存
     * @return mixed
     */
    public function saveGoodsEvaluations()
    {
        try {
            $userId = $this->getUserId();
            $userName = $this->user->getNickName();
            $plateformId = $this->getPlateformId();

            $goodsList = $this->getPostInputData('goodsList');
            $order = $this->getPostInputData('order');
            //传参验证
            if (is_null($goodsList) || empty($order)) {
                throw new \Exception('请传递有效goodsList');
            } else {
                foreach ($goodsList as $k => $v) {
                    if (empty($v['goods_sku']) || empty($v['content']) || empty($v['all_score'])) {
                        throw new \Exception('第' . ($k + 1) . '条请传递有效goods_sku或content或all_score');
                    }
                }
            };
            if (is_null($order) || empty($order)) {
                throw new \Exception('请传递有效order');
            } else {
                if (empty($order['order_no']) || empty($order['store_order_no']) || empty($order['content']) || empty($order['goods_score']) || empty($order['service_score']) || empty($order['speed_score'])) {
                    throw new \Exception('请传递有效goods_sku或content或all_score');
                }
                if (!is_int($order['goods_score']) || !is_int($order['goods_score']) || !is_int($order['goods_score'])) {
                    throw new \Exception('请传递有效整型goods_sku或content或all_score');
                }
            }
            $result = $this->goodsEvaluationService->saveEvaluations($userId, $userName, $plateformId, $goodsList, $order);
            if ($result['success']) {
                return $this->indexResp->ok()->send();
            } else {
                throw new \Exception($result['message']);
            }
        } catch (\Exception $e) {
            return $this->indexResp->err($e->getMessage())->send();
        }

    }


}