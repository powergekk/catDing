<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/17
 * Time: 15:15
 */
namespace app\store\controller;

use utils\PageUtils;
use utils\ReflectionUtils;
use app\common\service\AreaService;
use app\store\dto\CommitStoreRequestDto;
use app\common\service\StoreInfoService;
//use app\common\entity\database\StoreInfoDto;

class Platform extends Common
{
    /**
     * @var StoreInfoService
     */
    private $storeInfoService;

    /**
     * @var AreaService
     */
    private $areaService;

    public function _after_instance()
    {
        // TODO: Implement _after_instance() method.
        $this->storeInfoService = StoreInfoService::instance();
        $this->areaService = AreaService::instance();
    }

    /**
     * 返回地址信息
     * @return array
     */
    public function getAddress()
    {
        try {
            //1.如果省市区id为空，则返回所有的一级地域名
            //2.如果只传了省id，则返回对应的省信息，一级对应所有的二级（市）信息
            //3.如果传了省、市id，则返回对应的详细的省市区信息
            $data['provinceId'] = $this->request->param('data.provinceId/d', 0);
            $data['cityId'] = $this->request->param('data.cityId/d', 0);

            $res = $this->areaService->getInfo($data);

            return $this->indexResp->ok($res, '操作成功')->send();
        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }
    }

    public function units()
    {
//        $res = $this
    }

    /**
     * 店铺信息修改详情获取
     * @return array
     */
    public function storeInfo()
    {
        try {
            //店铺信息
            $storeInfo = $this->storeInfoService->getInfoByUserId($this->getUserId());
//            $storeInfo = ReflectionUtils::arrayToObj($storeInfo, StoreInfoDto::class);
//            $storeInfo = $this->getStoreUserSession()->getStoreInfo();
            $info['storeName'] = $storeInfo->stroe_name;
            $info['contacts'] = $storeInfo->connect_name;
            $info['phone'] = $storeInfo->connect_phone;
            $info['tel'] = $storeInfo->connect_number;
            $info['provinceId'] = $storeInfo->province_id;
            $info['provinceName'] = $storeInfo->province;
            $info['cityId'] = $storeInfo->city_id;
            $info['cityName'] = $storeInfo->city;
            $info['areaId'] = $storeInfo->area_id;
            $info['areaName'] = $storeInfo->area;
            $info['address'] = $storeInfo->address;

            return $this->indexResp->ok($info, '操作成功')->send();

        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }
    }

    /**
     * 店铺修改提交
     * @return array
     */
    public function commit()
    {
        /** @var $requestDataObj CommitStoreRequestDto;*/
        try {
            $requestDataObj = ReflectionUtils::arrayToObj($this->request->param('data/a'), CommitStoreRequestDto::class);

            if(!$requestDataObj->getTel() || !$requestDataObj->getProvinceId() || !$requestDataObj->getProvinceName() || !$requestDataObj->getCityId()
            || !$requestDataObj->getCityName() || !$requestDataObj->getAreaId() || !$requestDataObj->getAreaName() || !$requestDataObj->getAddress())
            {
                throw new \Exception('请填写所有必填项');
            }

            $res = $this->storeInfoService->editStoreByStoreNo($requestDataObj, $this->getStoreUserSession()->getStoreInfo()->getStoreNo());
            if($res === false)
                throw new \Exception('操作失败');

            return $this->indexResp->ok(null, '操作成功')->send();
        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }
    }

}