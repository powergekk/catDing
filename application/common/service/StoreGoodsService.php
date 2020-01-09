<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 15:56
 */

namespace app\common\service;


use app\common\base\traits\InstanceTrait;
use app\common\mapper\PlateformMapper;
use app\common\mapper\GoodsMapper;
use app\common\model\GoodsModel;
use app\common\consts\GoodsRelationType;
use app\common\mapper\store\StoreGoodsMapper;
use app\common\model\GoodsRelationModel;
use utils\PageUtils;

class StoreGoodsService extends ServiceAbstract
{
    use InstanceTrait;


    /**
     * @var StoreGoodsMapper
     */
    public $storeGoodsMapper;

    /**
     * @var GoodsMapper
     */
    private $goodsMapper;

    /**
     * @var PlateformMapper
     */
    public $plateformMapper;

    /**
     * @#name 用户信息
     * @var array
     */
    public $userInfo;

    /**
     * @#name 店铺No
     * @var string
     */
    public $storeNo;


    protected function _after_instance()
    {
        $this->storeGoodsMapper = StoreGoodsMapper::instance();
        $this->goodsMapper = GoodsMapper::instance();
        $this->plateformMapper = PlateformMapper::instance();
    }

    /**
     * 获取模板goods
     * @param array $storeCondition
     * @param array $limitCondition
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getModelGoods(array $storeCondition, array $limitCondition = [])
    {
        $res = $this->storeGoodsMapper->getModelSKu($storeCondition, $limitCondition);
        return $res;
    }


    /**
     * 获取店铺下的商品列表
     * @param array $whereCondition and条件
     * @param array $whereOrCondition or条件
     * @param int $page
     * @param int $pageSize
     * @return false|\PDOStatement|string|\think\Collection|array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getStoreGoodsList(array $whereCondition, array $whereOrCondition = [], int $page = 1, int $pageSize = 10)
    {
//        $allList = $this->storeGoodsMapper->getListByStoreNo($whereCondition, $page, $pageSize);
        $conditionList = $this->storeGoodsMapper->getGoodsByCondition($whereCondition, $whereOrCondition, $page, $pageSize);

//        $list = [];
//        if($conditionList)
//        {
//            foreach ($conditionList as $item)
//            {
//                $tmp['goodsId'] = $item->id;
//                $tmp['sku'] = $item->sku;
//                $tmp['goodsName'] = $item->name;
//                $tmp['goodsImage'] = $item->logo_pic;
//                $tmp['goodsSize'] = $item->spec;
//                $tmp['goodsPrice'] = $item->sale_price;
//                $tmp['stock'] = $item->qty;
//                $tmp['category'] = $item->category_name;
//                $tmp['units'] = $item->unit_name;
//                $tmp['status'] = $item->is_up === 0? 2 : 1; //上下架状态
//                $relationSku = $this->storeGoodsMapper->relationSku(['sku' => $tmp['sku']]);
//                $tmp['platformSku'] = '';
//                if(!empty($relationSku)) //关联了sku
//                    $tmp['platformSku'] = '关联sku-' . $relationSku[0]->sku;
//
//                $list[] = $tmp;
//            }
//        }

        return $conditionList;
    }

    /**
     * 按条件获取店铺商品的数目
     * @param array $whereCondition
     * @param array $whereOrCondition
     * @return int|string
     */
    public function getStoreGoodsCount(array $whereCondition, array $whereOrCondition = [])
    {
        return $this->storeGoodsMapper->countByCondition($whereCondition, $whereOrCondition);
    }

    /**
     * 生成随机的sku
     * @param int $length 长度
     * @param string $prefix 前缀
     * @param bool $mixed 是否字母混合
     * @return string
     */
    public function skuRandom(int $length = 5, string $prefix = 'SKU', bool $mixed = true)
    {
        $randomSku = $prefix;
        $numberArr = range(10, 99);
        shuffle($numberArr);
        $numbers = str_shuffle(join("", $numberArr));

        if($mixed)
        {
            $chars = str_shuffle(join("", range('a', 'z')));

            $randomSku .= substr(str_shuffle($chars.$numbers), mt_rand(0, 10), $length);
        } else
        {
            $randomSku .= substr($numbers, mt_rand(0, 10), $length);
        }
        return $randomSku;
    }

    /**
     * 新增或修改goods表商品
     * @param array $condition goodsId判断新增还是修改
     * @param string $storeNo 店铺编号
     * @return bool|false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function addOrEditGoods(array $condition, string $storeNo = '')
    {
        GoodsModel::startTrans();
        if(!isset($condition['id']) || !$condition['id'])
        {
            //随机生成一个sku
            $condition['sku'] = $this->skuRandom();
            while($this->storeGoodsMapper->getGoodsBySku(['storeNo' => $storeNo, 'goods_relation.sku' => ['in', $condition['sku']]]))
            {
                $condition['sku'] = $this->skuRandom();
            }
            //生成一条store数据
            $storeRes = $this->addStoreGoods($condition['sku'], GoodsRelationType::STORE, $storeNo);
            if($storeRes === false)
            {
                GoodsModel::rollback();
                return false;
            }

        }
        $res = $this->goodsMapper->addOrEditGoods($condition);
        if($res === false)
        {
            GoodsModel::rollback();
            return false;
        } else
        {
            GoodsModel::commit();
        }

        return $res;
    }

    /**
     * 根据sku获取goods信息
     * @param string $sku
     * @param int $platformId
     * @return GoodsModel|array|false|null|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsBySku(string $sku, int $platformId)
    {
        return $this->goodsMapper->getGoodsInfoBySku($sku, $platformId, '*');
    }

    /**
     * 新增relation关系
     * @param string $sku sku
     * @param string $relationType 关系类型，store/plateform/goods
     * @param string $relationValue 关系值
     * @return false|int
     */
    public function addStoreGoods(string $sku, string $relationType = GoodsRelationType::STORE, string $relationValue = '')
    {
        $condition = [];

        $condition['sku'] = $sku;
        $condition['relation_type'] = $relationType;
        $condition['relation_value'] =$relationValue;
        $condition['created_at'] = date('Y-m-d H:i:s');
        $condition['created_user_id'] = $this->userInfo['userId'];
        $condition['created_user_name'] = $this->userInfo['userName'];
        $condition['updated_user_id'] = 0;
        $condition['updated_user_name'] = '';
        $res = $this->storeGoodsMapper->addToGoodsRelation($condition);
        return $res;
    }

    /**
     *
     * @param array $condition
     * @param int $page
     * @param int $pageSize
     * @return false|\PDOStatement|string|\think\Collection|GoodsModel[]
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsByCondition(array $condition, int $page = 1, int $pageSize = 10)
    {
        return $this->goodsMapper->getGoodsByCondition($condition, $page, $pageSize);
    }

    /**
     * 通过id获取goods
     * @param array $goodsId goods表id
     * @param int $is_template 是否模板， 1模板 2不是模板
     * @return GoodsModel[]|false|\PDOStatement|string|\think\Collection|array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getGoodsById(array $goodsId, $is_template = 2)
    {
        $condition = ['id' => ['in', $goodsId], 'is_template' => $is_template];
        return $this->getGoodsByCondition($condition, 1, 999);
    }

    /**
     * 按条件获取商品总数
     * @param array $condition
     * @return int|string
     */
    public function getTotalByCondition(array $condition = [])
    {
        return $this->goodsMapper->countByCondition($condition);
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
     * 根据平台id获取goods信息
     * @param int $platformId
     * @param bool $isTemplate
     * @param array $param
     * @param $page
     * @param $pageSize
     * @param $total bool 是否需要总数
     * @return \Exception|false|\PDOStatement|string|\think\Collection|array
     */
    public function getGoodsByPlatform(int $platformId = 1, bool $isTemplate = false, array $param = [], $page = 1, $pageSize = 10, bool $total = false)
    {
        try{
            $platformInfo = $this->getPlatformById($platformId);
            $condition['platformNo'] = $platformInfo->plateform_no;
            $whereOrCondition = [];

            !empty($param['sku']) && $whereOrCondition['sku'] = ['LIKE', '%' . $param['sku'] . '%'];
            !empty($param['name']) && $whereOrCondition['name'] = ['LIKE', '%' . $param['name'] . '%'];
            !empty($param['spec']) && $whereOrCondition['spec'] = ['LIKE', '%'. $param['spec'] . '%'];

            $isTemplate && $condition['third_plateform_id'] = ['>', 0];
            return $this->storeGoodsMapper->getGoodsByPlatform($condition, $whereOrCondition, $page, $pageSize, $total);
        } catch (\Exception $exception)
        {
            return $exception;
        }
    }

    /**
     * 新增商品至goods与relation表
     * @param array $modelInfo 模板信息
     * @param string $storeNo 店铺号
     * @param int $platformId 平台号
     * @return bool|\Exception
     */
    public function addToGoodsAndRelation(array $modelInfo, string $storeNo = '', int $platformId = 1)
    {
        $data = date('Y-m-d H:i:s');
        $userId = $this->userInfo['userId'];
        $userName = $this->userInfo['userName'];

        $flag = true;

        try{
            //查出relation表中的platform是否有数据
            $platformRes = $this->getGoodsByPlatform($platformId, true)['data'];
            if(!empty($platformRes)) //有模板
            {
                $templateFromArr = array_column($platformRes, null, 'template_from_id');
            }

            GoodsModel::startTrans();
            foreach ($modelInfo as &$item)
            {
                $item->sku = $this->skuRandom(); //更换sku
                $item->is_template = 2;
                $item->template_from_id = $item->id;
                $item->created_at = $data;
                $item->updated_at = '';
                $item->created_user_id = $userId;
                $item->created_user_name = $userName;
                $item->updated_user_id = 0;
                $item->updated_user_name = '';
                $item->zhpt_goods_sku = 'zhpt_'.$item->sku;
                $item->third_goods_sku = 'third_'.$item->sku;
                $pKey = $item->getPk();
                $tmpId = $item->$pKey;

                $item->setAttr($pKey, null);

                //新增至goods表
                $goodsRes = $item->isUpdate(false)->save();

                $tmp['sku'] = $item->sku;
                $tmp['relation_type'] = GoodsRelationType::STORE;
                $tmp['relation_value'] = $storeNo;
                $tmp['created_at'] = $data;
                $tmp['created_user_id'] = $userId;
                $tmp['created_user_name'] = $userName;
                $tmp['updated_user_id'] = 0;
                $tmp['updated_user_name'] = '';

                //新增至relation表
                $relationRes = $this->storeGoodsMapper->addToGoodsRelation($tmp);

                $goodsRelationRes = [];
                if(isset($templateFromArr) && !empty($templateFromArr))
                {
                    //新增relation表的goods关系
                    if(in_array($tmpId, array_keys($templateFromArr)))
                    {
                        $tmp['relation_type'] = GoodsRelationType::GOODS;
                        $tmp['relation_value'] = $tmp['sku'];
                        $tmp['sku'] = $templateFromArr[$tmpId]->sku;
                        $goodsRelationRes = $this->storeGoodsMapper->addToGoodsRelation($tmp);
                    }
                }

                if($goodsRes === false || $relationRes === false || $goodsRelationRes === false)
                {
                    $flag = false;
                    break;
                }
            }

            if($flag === false)
            {
                GoodsModel::rollback();
                return false;
            } else
            {
                GoodsModel::commit();
                return true;
            }
        } catch (\Exception $exception)
        {
            return $exception;
        }

    }


    public function getIsMarkedGoods(int $platformId, int $isMarked, array $param = [], $page = 1, $pageSize = 10)
    {
        try {
            $platformGoodsList = $this->getGoodsByPlatform($platformId, false, $param, $page, $pageSize, true); //platform关系下goods列表
            $isMarkList = [];
            $relation = []; //标价关系
            if(empty($platformGoodsList['data']))
            {
                $goodsList = [];
            } else
            {
                //查出当前店铺下所有的goods
                $storeGoods = $this->getStoreGoodsList(['storeNo' => $this->storeNo], [], 1, 9999);

                //查出所有的goods关系下的goods信息
                $tmpList = $this->storeGoodsMapper->getGoodsByGoods(['sku' => array_column($storeGoods, 'sku')]);

                $platformSkuArr = array_column($platformGoodsList['data'], 'sku');
                $tmpSkuArr = array_unique(array_column($tmpList, 'sku'));


                if($isMarked === 3) //查看全部的可标记列表
                {
                    $goodsList = $platformGoodsList;
                } elseif ($isMarked === 2) //未标记的
                {
                    $notRelation = array_diff($platformSkuArr, $tmpSkuArr); //差集
                    if(empty($notRelation))
                    {
                        $goodsList['data'] = [];
                        $goodsList['total'] = 0;
                    } else
                    {
                        $goodsList['data'] = $this->goodsMapper->getGoodsByCondition(['sku' => ['in', $notRelation]], 1, 999);

                    }
                } elseif ($isMarked === 1) //已标记的
                {
                    $relationSku = array_intersect($platformSkuArr, $tmpSkuArr); //交集
                    if(empty($relationSku))
                    {
                        $goodsList = [];
                    } else
                    {
                        $goodsList = $this->goodsMapper->getGoodsByCondition(['sku' => ['in', $relationSku]], 1, 999);
                    }
                }
            }
            if(!empty($goodsList))
            {
                foreach ($goodsList as $item)
                {
                    $tmp['goodsId'] = $item->id;
                    $tmp['sku'] = $item->sku;
                    $tmp['goodsName'] = $item->name;
                    $tmp['goodsImage'] = $item->logo_pic;
                    $tmp['goodsSize'] = $item->spec;
                    $tmp['category'] = $item->category_name;
                    $tmp['units'] = $item->unit_name;
                    $isMarked == 1 && $tmp['isMarked'] = '';
                    $isMarkList[] = $tmp;
                }
            }
//            dump($tmpList);die;
            return $isMarkList;

        } catch (\Exception $exception)
        {
            return $exception;
        }
    }

}