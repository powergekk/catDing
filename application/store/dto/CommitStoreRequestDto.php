<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/20
 * Time: 17:24
 */
namespace app\store\dto;

class CommitStoreRequestDto
{
    /**
     * @#name 固定电话
     * @var string
     * @require
     */
    private $tel = '';


    /**
     * @#name 省id
     * @var int
     * @require
     */
    private $provinceId = 0;


    /**
     * @#name 省名
     * @var string
     * @require
     */
    private $provinceName = '';


    /**
     * @#name 市id
     * @var int
     * @require
     */
    private $cityId = 0;


    /**
     * @#name 市名
     * @var string
     * @require
     */
    private $cityName = '';


    /**
     * @#name 区id
     * @var int
     * @require
     */
    private $areaId = 0;


    /**
     * @#name 区名
     * @var string
     * @require
     */
    private $areaName = '';


    /**
     * @#name 详细地址
     * @var string
     * @require
     */
    private $address = '';

    /**
     * @return string
     */
    public function getTel(): string
    {
        return $this->tel;
    }

    /**
     * @param string $tel
     */
    public function setTel(string $tel): void
    {
        $this->tel = $tel;
    }

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
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

}