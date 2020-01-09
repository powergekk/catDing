<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/4
 * Time: 18:03
 */

namespace app\common\entity\database;


use app\common\base\JsonSerializableAbstract;


/**
 * Class StoreInfoDto
 * @notRequire
 * @package app\common\entity\database
 */
class StoreInfoDto extends JsonSerializableAbstract
{

    /**
     * @#name 主键
     * @var int
     */
    protected $id = 0;


    /**
     * @#name 用户ID
     * @var int
     */
    protected $user_id = 0;


    /**
     * @#name 店铺编号
     * @var string
     */
    protected $store_no = '';

    /**
     * @#name 店铺名
     * @var string
     */
    protected $stroe_name = '';

    /**
     * @#name 省份
     * @var string
     */
    protected $province = '';

    /**
     * @#name 省份ID
     * @var int
     */
    protected $province_id = 0;


    /**
     * @#name 城市地区
     * @var string
     */
    protected $city = '';


    /**
     * @#name 城市ID
     * @var int
     */
    protected $city_id = 0;

    /**
     * @#name 区域
     * @var string
     */
    protected $area = '';


    /**
     * @#name 区域ID
     * @var int
     */
    protected $area_id = 0;


    /**
     * @#name 店铺地址
     * @var string
     */
    protected $address = '';


    /**
     * #name 维度
     * @var float
     */
    protected $latitude = 0.0;

    /**
     * @#name 经度
     * @var float
     */
    protected $longitude = 0.0;


    /**
     * @#name 经纬度类型
     * @var string
     */
    protected $tude_type = '';


    /**
     * @#name 联系人
     * @var string
     */
    protected $connect_name = '';

    /**
     * @#name 联系人电话
     * @var string
     */
    protected $connect_phone = '';


    /**
     * @#name 联系人座机
     * @var string
     */
    protected $connect_number = '';

    /**
     * @#name 创建时间
     * @var string
     */
    protected $created_at = '';


    /**
     * @#name 更新时间
     * @var string
     */
    protected $updated_at = '';


    /**
     * @#name 创建用户id
     * @var int
     */
    protected $created_user_id = 0;


    /**
     * @#name 创建用户名称
     * @var string
     */
    protected $created_user_name = '';

    /**
     * @#name 更新用户id
     * @var int
     */
    protected $updated_user_id = 0;


    /**
     * @#name 更新用户名称
     * @var string
     */
    protected $updated_user_name = '';


    /**
     * @#name 删除状态
     * @var string
     */
    protected $del_status = '';

    /**
     * @#name 状态
     * @var string
     */
    protected $status = '';


    /**
     * @#name 审核意见
     * @var string
     */
    protected $remark = '';

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getStoreNo(): string
    {
        return $this->store_no;
    }

    /**
     * @param string $store_no
     */
    public function setStoreNo(string $store_no): void
    {
        $this->store_no = $store_no;
    }

    /**
     * @return string
     */
    public function getStroeName(): string
    {
        return $this->stroe_name;
    }

    /**
     * @param string $stroe_name
     */
    public function setStroeName(string $stroe_name): void
    {
        $this->stroe_name = $stroe_name;
    }

    /**
     * @return string
     */
    public function getProvince(): string
    {
        return $this->province;
    }

    /**
     * @param string $province
     */
    public function setProvince(string $province): void
    {
        $this->province = $province;
    }

    /**
     * @return int
     */
    public function getProvinceId(): int
    {
        return $this->province_id;
    }

    /**
     * @param int $province_id
     */
    public function setProvinceId(int $province_id): void
    {
        $this->province_id = $province_id;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return int
     */
    public function getCityId(): int
    {
        return $this->city_id;
    }

    /**
     * @param int $city_id
     */
    public function setCityId(int $city_id): void
    {
        $this->city_id = $city_id;
    }

    /**
     * @return string
     */
    public function getArea(): string
    {
        return $this->area;
    }

    /**
     * @param string $area
     */
    public function setArea(string $area): void
    {
        $this->area = $area;
    }

    /**
     * @return int
     */
    public function getAreaId(): int
    {
        return $this->area_id;
    }

    /**
     * @param int $area_id
     */
    public function setAreaId(int $area_id): void
    {
        $this->area_id = $area_id;
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

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    /**
     * @return string
     */
    public function getTudeType(): string
    {
        return $this->tude_type;
    }

    /**
     * @param string $tude_type
     */
    public function setTudeType(string $tude_type): void
    {
        $this->tude_type = $tude_type;
    }

    /**
     * @return string
     */
    public function getConnectName(): string
    {
        return $this->connect_name;
    }

    /**
     * @param string $connect_name
     */
    public function setConnectName(string $connect_name): void
    {
        $this->connect_name = $connect_name;
    }

    /**
     * @return string
     */
    public function getConnectPhone(): string
    {
        return $this->connect_phone;
    }

    /**
     * @param string $connect_phone
     */
    public function setConnectPhone(string $connect_phone): void
    {
        $this->connect_phone = $connect_phone;
    }

    /**
     * @return string
     */
    public function getConnectNumber(): string
    {
        return $this->connect_number;
    }

    /**
     * @param string $connect_number
     */
    public function setConnectNumber(string $connect_number): void
    {
        $this->connect_number = $connect_number;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * @param string $created_at
     */
    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    /**
     * @param string $updated_at
     */
    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return int
     */
    public function getCreatedUserId(): int
    {
        return $this->created_user_id;
    }

    /**
     * @param int $created_user_id
     */
    public function setCreatedUserId(int $created_user_id): void
    {
        $this->created_user_id = $created_user_id;
    }

    /**
     * @return string
     */
    public function getCreatedUserName(): string
    {
        return $this->created_user_name;
    }

    /**
     * @param string $created_user_name
     */
    public function setCreatedUserName(string $created_user_name): void
    {
        $this->created_user_name = $created_user_name;
    }

    /**
     * @return int
     */
    public function getUpdatedUserId(): int
    {
        return $this->updated_user_id;
    }

    /**
     * @param int $updated_user_id
     */
    public function setUpdatedUserId(int $updated_user_id): void
    {
        $this->updated_user_id = $updated_user_id;
    }

    /**
     * @return string
     */
    public function getUpdatedUserName(): string
    {
        return $this->updated_user_name;
    }

    /**
     * @param string $updated_user_name
     */
    public function setUpdatedUserName(string $updated_user_name): void
    {
        $this->updated_user_name = $updated_user_name;
    }

    /**
     * @return string
     */
    public function getDelStatus(): string
    {
        return $this->del_status;
    }

    /**
     * @param string $del_status
     */
    public function setDelStatus(string $del_status): void
    {
        $this->del_status = $del_status;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getRemark(): string
    {
        return $this->remark;
    }

    /**
     * @param string $remark
     */
    public function setRemark(string $remark): void
    {
        $this->remark = $remark;
    }




}