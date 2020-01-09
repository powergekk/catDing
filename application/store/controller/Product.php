<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/28
 * Time: 15:33
 */
namespace app\store\controller;

use app\common\consts\GoodsRelationType;
use utils\PageUtils;
use utils\ReflectionUtils;
use app\store\dto\SaleRequestDto;
use app\store\dto\ChangeRequestDto;
use app\store\dto\PlatformRequestDto;
use app\common\service\StoreGoodsService;
use app\common\exceptions\ParamsRuntimeException;


class Product extends Common
{
    protected $notCheckLoginAction = ''; //是否需要验证登录


    /**
     * @var StoreGoodsService
     */
    private $storeGoodsService;


    public function _after_instance()
    {
        // TODO: Implement _after_instance() method.
        $this->storeGoodsService = StoreGoodsService::instance();
        $this->storeGoodsService->userInfo = ['userId' => $this->getUserId(), 'userName' => $this->getUserSession()->getNickName()];
        $this->storeGoodsService->storeNo = $this->getStoreUserSession()->getStoreInfo()->getStoreNo();
    }

    /**
     * 店铺商品列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        //参数
        $data['status'] = $this->request->param('data.status/d', 1); //状态， 默认为1表示全部， 2.上架 3.下架
        $data['searchContents'] = $this->request->param('data.searchContents', ''); //搜索内容

        $page = $this->getPage(); //获取页码
        $pageSize = $this->getPageSize(); //获取每页
        $storeNo = $this->getStoreUserSession()->getStoreInfo()->getStoreNo(); //当前用户对应的店铺号
        $whereCondition = []; //and条件
        $whereOrCondition = []; //or条件
        $whereCondition['storeNo'] = $storeNo; //必填项
        if($data['status'] === 2)
        {
            $whereCondition['goods.is_up'] = 1;
        } elseif ($data['status'] === 3)
        {
            $whereCondition['goods.is_up'] = 0;
        }
        $data['searchContents'] && $whereOrCondition = ['sku' => ['LIKE', '%'.$data['searchContents'].'%'], 'name' =>['LIKE', '%'.$data['searchContents'].'%'], 'spec' => ['LIKE', '%'.$data['searchContents'].'%']];

        $conditionList = $this->storeGoodsService->getStoreGoodsList($whereCondition, $whereOrCondition, $page, $pageSize);

        $list = [];
        if($conditionList)
        {
            foreach ($conditionList as $item)
            {
                $tmp['goodsId'] = $item->id;
                $tmp['sku'] = $item->sku;
                $tmp['goodsName'] = $item->name;
                $tmp['goodsImage'] = $item->logo_pic;
                $tmp['goodsSize'] = $item->spec;
                $tmp['goodsPrice'] = floatval($item->sale_price);
                $tmp['stock'] = $item->qty;
                $tmp['category'] = $item->category_name;
                $tmp['units'] = $item->unit_name;
                $tmp['status'] = $item->is_up === 0? 2 : 1; //上下架状态
                $relationSku = $this->storeGoodsService->storeGoodsMapper->relationSku(['sku' => $tmp['sku']]);
                $tmp['platformSku'] = '';
                if(!empty($relationSku)) //关联了sku
                {
                    $platformName = $this->storeGoodsService->getPlatformById($this->getPlateformId())->name;
                    $tmp['platformSku'] = $platformName . ':' . $relationSku[0]->sku;
                }


                $list[] = $tmp;
            }
        }

        $total = $this->storeGoodsService->getStoreGoodsCount($whereCondition, $whereOrCondition);

        $ret = PageUtils::formatPageResult($page, $pageSize, $total, $list);
        return $this->indexResp->ok($ret, '操作成功')->send();
    }

    /**
     * 新增、修改
     * @return array
     */
    public function operate()
    {
        try {
            //参数
            $data['goodsId'] = $this->request->param('data.goodsId/d', 0); //主键id，默认为0，不传或者为0时表示新增，否则表示修改
            $data['goodsName'] = $this->request->param('data.goodsName');
            $data['goodsImage'] = $this->request->param('data.goodsImage');
            $data['goodsSize'] = $this->request->param('data.goodsSize');
            $data['goodsPrice'] = $this->request->param('data.goodsPrice');
            $data['stock'] = $this->request->param('data.stock/d');
            $data['category_id'] = $this->request->param('data.category_id/d', 0);
            $data['category_name'] = $this->request->param('data.category_name');
            $data['unitsId'] = $this->request->param('data.unitsId/d', 0);
            $data['unitsName'] = $this->request->param('data.unitsName', '');
            $data['bannerImage'] = $this->request->param('data.bannerImage/a');
            $data['details'] = $this->request->param('data.details');

            $condition = [];
            $condition['name'] = $data['goodsName'];
            $condition['desc'] = $data['details'];
            $condition['short'] = '';
            $condition['logo_pic'] = $data['goodsImage'];
            $condition['unit_id'] = $data['unitsId'];
            $condition['unit_name'] = $data['unitsName'];
            $condition['brand_id'] = 0;
            $condition['brand_name'] = '';
            $condition['category_id'] = $data['category_id'];
            $condition['category_name'] = $data['category_name'];
            $condition['spec'] = $data['goodsSize'];
            $condition['attr'] = '';
            $condition['status'] = '';


            $condition['market_price'] = 0.0;
            $condition['original_price'] = 0.0;
            $condition['sale_price'] = $data['goodsPrice'];
            $condition['zhpt_goods_sku'] = 'zhpt'.$this->storeGoodsService->skuRandom(); //待定
            $condition['zhpt_plateform_id'] = $this->getPlateformId();
            $condition['zhpt_status'] = '';
            $condition['third_goods_sku'] = $this->storeGoodsService->skuRandom(); //待定
            $condition['third_status'] = '';
            $condition['third_price'] = 0.0;
            $condition['third_plateform_id'] = $this->getPlateformId();
            $condition['qty'] = $data['stock'];
            //判断新增还是修改
            if($data['goodsId'])
            {
                //修改
                $condition['id'] = $data['goodsId'];
                $condition['updated_at'] = date('Y-m-d H:i:s');
                $condition['updated_user_id'] = $this->getUserId();
                $condition['updated_user_name'] = $this->getUserSession()->getNickName();
            } else
            {
                //新增
                $condition['created_at'] = date('Y-m-d H:i:s');
                $condition['created_user_id'] = $this->getUserId();
                $condition['created_user_name'] = $this->getUserSession()->getNickName();
            }

            //具体操作

            $res = $this->storeGoodsService->addOrEditGoods($condition, $this->getStoreUserSession()->getStoreInfo()->getStoreNo());

            if($res !== false)
            {
                return $this->indexResp->ok(null, '操作成功')->send();
            } else
            {
                return $this->indexResp->err('操作失败')->send();
            }

        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }
    }

    /**
     * 可用模板列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function model()
    {
        $searchContents = $this->request->param('data.searchContents');
        $page = $this->getPage();
        $pageSize = $this->getPageSize();

        $storeNo = $this->getStoreUserSession()->getStoreInfo()->getStoreNo(); //当前用户对应的店铺号

        $whereCondition = []; //and条件
        $whereCondition['storeNo'] = $storeNo; //必填项
        //设置可预览的模板条件
        $storeRes = $this->createModelCondition($whereCondition, $searchContents);

        $list = [];
        if(!$storeRes['conditionId'] || is_null($storeRes['conditionId']))
        {
            $list = [];
            $total = 0;
        }else
        {
            foreach ($storeRes['conditionId'] as $item)
            {
                $tmp['goodsId'] = $item->id;
                $tmp['sku'] = $item->sku;
                $tmp['goodsName'] = $item->name;
                $tmp['goodsImage'] = $item->logo_pic;
                $tmp['goodsSize'] = $item->spec;
                $tmp['category'] = $item->category_name;
                $tmp['units'] = $item->unit_name;
                $tmp['check'] = 'N';

                $list[] = $tmp;
            }
            $total = $storeRes['total'];
        }


        $ret = PageUtils::formatPageResult($page, $pageSize, $total, $list);
        return $this->indexResp->ok($ret, '操作成功')->send();
    }

    /**
     * 分页获取可用模板
     * @param array $storeCondition
     * @param string $searchCondition
     * @return null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function createModelCondition(array $storeCondition, string $searchCondition = '')
    {
        //先找出当前店铺下的所有的sku
        $storeCondition = array_merge($storeCondition, ['template_from_id' => ['>', 0]]);
        $storeList = $this->storeGoodsService->getModelGoods($storeCondition);

        if(empty($storeList))
            return null;


        //通过这些由模板生成的sku查出模板
        $id = [];
        foreach ($storeList as $modelSku)
        {
            $id[] = $modelSku->template_from_id;
        }

        $where = [
            'is_template' => 1,
            'id' => ['not in', $id]
        ];
        !empty($searchCondition) && $where = array_merge($where, ['sku|name|spec' => ['LIKE', '%' . $searchCondition . '%']]);

        $changedModel = $this->storeGoodsService->getGoodsByCondition($where, $this->getPage(), $this->getPageSize());
        $total = $this->storeGoodsService->getTotalByCondition($where);


        $res['conditionId'] = $changedModel;
        $res['total'] = $total;

        return $res;
    }

    /**
     * 模板生成商品提交操作
     * @return array
     */
    public function commit()
    {
        $goodsList = $this->request->param('data.goodsList/a'); //模板列表，数组

        $goodsIdArr = array_column($goodsList, 'goodsId'); //模板的主键id集合

        if(empty($goodsIdArr))
        {
            return $this->indexResp->err('请先勾选模板')->send();
        }

        try
        {
            //先查询出模板的所有信息
            $modelInfo = $this->storeGoodsService->getGoodsById($goodsIdArr, 1);
            if(empty($modelInfo)) //找不到数据，抛出异常
            {
                throw new ParamsRuntimeException();
            }
            $storeNo = $this->getStoreUserSession()->getStoreInfo()->getStoreNo();

            //新增数据到goods表和relation表
            $res = $this->storeGoodsService->addToGoodsAndRelation($modelInfo, $storeNo, $this->getPlateformId());

            if($res === false)
            {
                throw new \Exception('数据存储错误');
            }
            if($res instanceof \Exception)
                throw new \Exception($res->getMessage());

            return $this->indexResp->ok(null, '操作成功')->send();

        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }

    }

    /**
     * 调整库存/供价
     * @return array
     */
    public function change()
    {
        try {
            //将请求参数转换为对象
            $requestDataObj = ReflectionUtils::arrayToObj($this->request->param('data/a'), ChangeRequestDto::class);

            $goodsInfo = $this->storeGoodsService->getGoodsById([$requestDataObj->getGoodsId()]);

            if($goodsInfo === false || empty($goodsInfo))
            {
                throw new \Exception($requestDataObj->getGoodsId().'未查到任何商品数据');
            }
            $condition = $requestDataObj->setType(); //设置修改请求参数
            $condition['id'] = $requestDataObj->getGoodsId();

            $res = $this->storeGoodsService->addOrEditGoods($condition);

            if($res === false)
            {
                throw new \Exception('数据修改失败');
            } elseif ($res === 0)
            {
                throw new \Exception('数据不能相同');
            }
            return $this->indexResp->ok('修改成功')->send();
        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }

    }

    /**
     * 上、下架
     * @return array
     */
    public function sale()
    {
        try {
            $requestDataObj = ReflectionUtils::arrayToObj($this->request->param('data/a'), SaleRequestDto::class);
            $goodsInfo = $this->storeGoodsService->getGoodsById($requestDataObj->getGoodsId());
            if($goodsInfo === false || empty($goodsInfo))
            {
                throw new \Exception($requestDataObj->getGoodsId().'未查到任何商品数据');
            }

            $condition = $requestDataObj->setSale();
            $condition['id'] = $requestDataObj->getGoodsId();

            $res = $this->storeGoodsService->addOrEditGoods($condition);

            if($res === false)
            {
                throw new \Exception('操作失败');
            } elseif($res === 0)
            {
                throw new \Exception('不能重复操作');
            }

            return $this->indexResp->ok('操作成功')->send();
        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }

    }

    public function platform()
    {
        try {
            $requestDataObj = ReflectionUtils::arrayToObj($this->request->param('data/a'), PlatformRequestDto::class);

            $param['sku'] = $requestDataObj->getSku();
            $param['name'] = $requestDataObj->getGoodsName();
            $param['spec'] = $requestDataObj->getGoodsSize();

//            $isMarked = $requestDataObj->getIsMarked() === 3?

            $list = $this->storeGoodsService->getIsMarkedGoods($this->getPlateformId(), $requestDataObj->getIsMarked(), $param, $requestDataObj->getPage(), $requestDataObj->getPageSize());
            //platform下的sku列表
//            $platformGoodsList = $this->storeGoodsService->getGoodsByPlatform($this->getPlateformId(), false, $param);


            //goods关系下的sku列表
//            $goodsList = $this->storeGoodsService->getGoodsByCondition();

//            dump($platformGoodsList);die;

        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }
    }

    /**
     * 关联操作
     * @return array
     */
    public function mark()
    {
        $goodsId = $this->request->param('data.goodsId/d'); //用户选择的需要关联的平台sku id
        $goodsSku = $this->request->param('data.sku'); //需要被关联的sku

        try {
            if(empty($goodsSku))
            {
                throw new \Exception('被关联的sku缺失');
            }
            //获取goods信息,判断是否存在goods
            $goodsInfo = $this->storeGoodsService->getGoodsById([$goodsId]);

            if(empty($goodsInfo))
            {
                throw new \Exception('要关联的sku不存在');
            }

            $goodsSkuInfo = $this->storeGoodsService->getGoodsBySku($goodsSku, $this->getPlateformId());

            if(empty($goodsSkuInfo))
            {
                throw new \Exception('被关联的sku不存在');
            }

            $res = $this->storeGoodsService->addStoreGoods($goodsInfo[0]->sku, GoodsRelationType::GOODS, $goodsSku);
            if($res === false)
            {
                throw new \Exception('关联失败');
            }

            return $this->indexResp->ok(null, '关联成功')->send();
        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }
    }


}