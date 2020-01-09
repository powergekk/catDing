<?php

namespace app\common\service;

use app\common\base\traits\InstanceTrait;
use app\common\bean\AreaAddressDto;
use app\common\consts\AreaLevel;
use app\common\mapper\AreaMapper;

class AreaService extends ServiceAbstract
{
    use InstanceTrait;

    /**
     * @var AreaMapper
     */
    private $areaMapper;

    /**
     * 实例化后调用函数
     */
    protected function _after_instance()
    {
        $this->areaMapper = AreaMapper::instance();
    }

    /**
     * 根据传值获取不同的地址信息
     * @param array $condition
     * @return array
     * @throws \Exception
     */
    public function getInfo(array $condition = [])
    {
        try {
            $infoList = [];
            if(!$condition['provinceId'] && !$condition['cityId']) //获取所有的省
            {
                $res = $this->getProvinceList();
                if(empty($res))
                    throw new \Exception('没有省信息');
                foreach ($res as $item)
                {
                    $info['provinceId'] = $item->id;
                    $info['provinceName'] = $item->area_name;
                    $info['cityId'] = 0;
                    $info['cityName'] = '';
                    $info['areaId'] = 0;
                    $info['areaName'] = '';
                    $infoList[] = $info;
                }
            } elseif ($condition['provinceId'] && !$condition['cityId']) //获取省、市
            {
                //获取当前省信息
                $province = $this->getInfoById($condition['provinceId']);
                //获取对应的所有市信息
                $city = $this->getChildList($condition['provinceId']);

                foreach ($city as $item)
                {
                    $info['provinceId'] = $condition['provinceId'];
                    $info['provinceName'] = $province->area_name;
                    $info['cityId'] = $item->id;
                    $info['cityName'] = $item->area_name;
                    $info['areaId'] = 0;
                    $info['areaName'] = '';
                    $infoList[] = $info;
                }
            } elseif ($condition['provinceId'] && $condition['cityId']) //获取详细的省市区
            {
                //获取当前省信息
                $province = $this->getInfoById($condition['provinceId']);
                //获取市信息
                $city = $this->getInfoById($condition['cityId']);
                //获取对应的所有区信息
                $area = $this->getChildList($condition['cityId']);

                foreach ($area as $item)
                {
                    $info['provinceId'] = $condition['provinceId'];
                    $info['provinceName'] = $province->area_name;
                    $info['cityId'] = $city->id;
                    $info['cityName'] = $city->area_name;
                    $info['areaId'] = $item->id;
                    $info['areaName'] = $item->area_name;
                    $infoList[] = $info;
                }
            } else
            {
                throw new \Exception('错误的传值组合');
            }
            return $infoList;
        } catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    /**
     * 通过id值获得地区相应的下一级地区结果集
     * @param int $id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getChildList(int $id)
    {
        $childrenList = $this->areaMapper->getListById($id);

        return $childrenList;
    }


    /**
     * 获取所有的省
     * @return false|\PDOStatement|string|\think\Collection|array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProvinceList()
    {
        $childrenList = $this->getChildList(0);
        return $childrenList;
    }


    /**
     * 通过省市区的名字时间查询得到ID
     * @param string $province
     * @param string $city
     * @param string $area
     * @return AreaAddressDto|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAreasByNames(string $province, string $city, string $area)
    {
        if (empty($province) || empty($city) || empty($area)) {
            return false;
        }

        //省
        $provinceModel = $this->areaMapper->getInfoByName($province, AreaLevel::PROVINCE_LEVEL);
        if (empty($provinceModel)) {
            return false;
        }
        $cityModel = $this->areaMapper->getInfoByName($city, AreaLevel::CITY_LEVEL, $provinceModel->getAttr("id"));
        if (empty($cityModel)) {
            return false;
        }
        $areaModel = $this->areaMapper->getInfoByName($area, AreaLevel::AREA_LEVEL, $cityModel->getAttr("id"));
        if (empty($areaModel)) {
            return false;
        }
        $areaAddressDto = new AreaAddressDto();
        $areaAddressDto->setProvince($provinceModel);
        $areaAddressDto->setProvinceId($provinceModel->getAttr('id'));
        $areaAddressDto->setProvinceName($provinceModel->getAttr('area_name'));
        $areaAddressDto->setCity($cityModel);
        $areaAddressDto->setCityId($cityModel->getAttr('id'));
        $areaAddressDto->setCityName($cityModel->getAttr('area_name'));
        $areaAddressDto->setArea($areaModel);
        $areaAddressDto->setAreaId($areaModel->getAttr('id'));
        $areaAddressDto->setAreaName($areaModel->getAttr('area_name'));

        return $areaAddressDto;
    }


    /**
     * 通过地区ID查询地区信息
     * @param int $id
     * @return \app\common\model\AreaModel|array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfoById(int $id)
    {
        return $this->areaMapper->getInfoById($id);
    }


}