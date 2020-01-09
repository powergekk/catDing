<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/12
 * Time: 22:48
 */

namespace app\common\bean;


use app\common\model\AreaModel;
use app\common\service\AreaService;

class AreaAddressDto
{

    /**
     * #{省份ID}
     * @var int
     */
    private $provinceId;


    /**
     * #{省份}
     * @var string
     */
    private $provinceName;

    /**
     * #{市ID}
     * @var int
     */
    private $cityId;


    /**
     * #{市}
     * @var string
     */
    private $cityName;


    /**
     * #{区ID}
     * @var int
     */
    private $areaId;


    /**
     * #{区}
     * @var string
     */
    private $areaName;


    /**
     * #{省的model}
     * @var AreaModel
     */
    private $province;


    /**
     * #{市的model}
     * @var AreaModel
     */
    private $city;


    /**
     * #{区的model}
     * @var AreaModel
     */
    private $area;

    /**
     * @return int
     */
    public function getProvinceId(): int
    {
        return $this->provinceId;
    }

    /**
     * @param int $provinceId
     */
    public function setProvinceId(int $provinceId): void
    {
        $this->provinceId = $provinceId;
    }

    /**
     * @return string
     */
    public function getProvinceName(): string
    {
        return $this->provinceName;
    }

    /**
     * @param string $provinceName
     */
    public function setProvinceName(string $provinceName): void
    {
        $this->provinceName = $provinceName;
    }

    /**
     * @return int
     */
    public function getCityId(): int
    {
        return $this->cityId;
    }

    /**
     * @param int $cityId
     */
    public function setCityId(int $cityId): void
    {
        $this->cityId = $cityId;
    }

    /**
     * @return string
     */
    public function getCityName(): string
    {
        return $this->cityName;
    }

    /**
     * @param string $cityName
     */
    public function setCityName(string $cityName): void
    {
        $this->cityName = $cityName;
    }

    /**
     * @return int
     */
    public function getAreaId(): int
    {
        return $this->areaId;
    }

    /**
     * @param int $areaId
     */
    public function setAreaId(int $areaId): void
    {
        $this->areaId = $areaId;
    }

    /**
     * @return string
     */
    public function getAreaName(): string
    {
        return $this->areaName;
    }

    /**
     * @param string $areaName
     */
    public function setAreaName(string $areaName): void
    {
        $this->areaName = $areaName;
    }


    /**
     * @return AreaModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getProvince(): AreaModel
    {
        return $this->getAreaModel('province');
    }

    /**
     * @param AreaModel $province
     */
    public function setProvince(AreaModel $province): void
    {
        $this->province = $province;
    }


    /**
     * @return AreaModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCity(): AreaModel
    {
        return $this->getAreaModel('city');
    }

    /**
     * @param AreaModel $city
     */
    public function setCity(AreaModel $city): void
    {
        $this->city = $city;
    }


    /**
     * @return AreaModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getArea(): AreaModel
    {
        return $this->getAreaModel('area');
    }

    /**
     * @param AreaModel $area
     */
    public function setArea(AreaModel $area): void
    {
        $this->area = $area;
    }


    /**
     * 返回[或通过ID查询]
     * @param string $name
     * @return mixed|AreaModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getAreaModel(string $name)
    {
        $nameId = $name . 'Id';
        $nameName = $name . 'Name';
        if (is_null($this->$name)) {
            if (!is_null($this->$nameId)) {
                $this->$name = AreaService::instance()->getInfoById($this->$nameId);
                if (!empty($this->$name)) {
                    $this->$nameName = $this->$name->getAttr('area_name');
                }
            }
        }
        return $this->$name;
    }
}