<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 14:53
 */

namespace app\common\service;

use app\common\base\traits\InstanceTrait;
use app\common\mapper\UserAddressMapper;
use app\common\model\AreaModel;
use app\common\model\UserAddressModel;
use app\common\validate\TudeAddressValidate;
use app\common\validate\UserAddressValidate;
use utils\ArrayUtils;

class UserAddressService extends ServiceAbstract
{
    use InstanceTrait;

    /**
     * @var userAddressMapper
     */
    private $userAddressMapper;


    /**
     * @var AreaService
     */
    private $areaService;

    /**
     * 实例化后调用函数
     */
    protected function _after_instance()
    {
        $this->userAddressMapper = UserAddressMapper::instance();
        $this->areaService = AreaService::instance();
    }


    /**
     * 获取用户在当前平台的收货地址列表
     * @param int $userId
     * @param int $plateformId
     * @param int $page
     * @param int $pageSize
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAddressListById(int $userId, int $plateformId, int $page = 1, int $pageSize = 10)
    {
        return $this->userAddressMapper->getAddressListById($userId, $plateformId, $page, $pageSize);
    }


    /**
     * 新增记录
     * @param int $userId
     * @param int $plateformId
     * @param array $addressData
     * @param int $doUserId
     * @param string $doUserName
     * @return UserAddressModel|array|bool|false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function addUserAddress(int $userId, int $plateformId, array $addressData, int $doUserId, string $doUserName)
    {
        $userAddressModel = new UserAddressModel();
        return $this->saveUserAddress($userId, $plateformId, $doUserId, $doUserName, $userAddressModel, $addressData);
    }


    /**
     * 修改记录
     * @param int $userId
     * @param int $id
     * @param array $addressData
     * @param int $plateformId
     * @param int $doUserId
     * @param string $doUserName
     * @return UserAddressModel|array|bool|false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function updateUserAddress(int $id, int $userId, int $plateformId, array $addressData, int $doUserId, string $doUserName)
    {
        $userAddressModel = $this->userAddressMapper->getAddressById($id, $userId, $plateformId);
        if (empty($userAddressModel)) {
            return ['记录ID:[' . $id . ']不存在'];
        }
        return $this->saveUserAddress($userId, $plateformId, $doUserId, $doUserName, $userAddressModel, $addressData);
    }


    /**
     * 删除地址
     * @param int $userId
     * @param int $plateformId
     * @param int $id
     * @return bool
     */
    public function del(int $id, int $userId, int $plateformId, int $doUserId, string $doUserName)
    {
        return $this->userAddressMapper->delAddress($id, $userId, $plateformId, $doUserId, $doUserName);
    }


    /**
     * 设置为默认
     * @param int $userId
     * @param int $plateformId
     * @param int $id
     * @param int $doUserId
     * @param string $doUserName
     * @param bool $isDefault
     * @return bool
     */
    public function setDefault(int $id, int $userId, int $plateformId, int $doUserId, string $doUserName, bool $isDefault = true)
    {
        if ($isDefault) {
            $this->userAddressMapper->setOtherNotDefaultAddress($id, $userId, $plateformId, $doUserId, $doUserName);
        }
        $this->userAddressMapper->setDefaultAddress($id, $userId, $plateformId, $doUserId, $doUserName, $isDefault);
        return true;
    }

    /**
     * 向MODEL里修改或增加新数据
     * @param int $userId
     * @param int $plateformId
     * @param int $doUserId
     * @param string $doUserName
     * @param UserAddressModel $userAddressModel
     * @param array $addressData
     * @return UserAddressModel|array|bool|false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function saveUserAddress(int $userId, int $plateformId, int $doUserId, string $doUserName, UserAddressModel $userAddressModel, array $addressData)
    {
        //验证地区
        $province = ArrayUtils::getVal($addressData, 'province', '');
        $city = ArrayUtils::getVal($addressData, 'city', '');
        $area = ArrayUtils::getVal($addressData, 'area', '');

        $areaAddressDto = $this->areaService->getAreasByNames($province, $city, $area);
        if (empty($areaAddressDto)) {
            return ['省市区匹配异常'];
        }
        //设定省市区的ID
        ArrayUtils::setVal($addressData, 'province_id', $areaAddressDto->getProvinceId());
        ArrayUtils::setVal($addressData, 'city_id', $areaAddressDto->getCityId());
        ArrayUtils::setVal($addressData, 'area_id', $areaAddressDto->getAreaId());

        $addressData['address'] = empty($addressData['address']) ? '' : $addressData['address'];

        $addressData['plateform_id'] = $plateformId;
        $addressData['user_id'] = $userId;
        $check = $this->checkData($addressData);

        //判断数据验证是否异常
        if (is_array($check)) {
            return $check;
        }

        if ($userAddressModel->getAttrVal($userAddressModel->getPk()) > 0) {
            $userAddressModel->setAttr('updated_user_id', $doUserId);
            $userAddressModel->setAttr('updated_user_name', $doUserName);
            $fields = [
                'name',
                'tel',
                'province',
                'province_id',
                'city',
                'city_id',
                'area',
                'area_id',
                'address',
                'is_default',
                'latitude',
                'longitude',
                'tude_type'
            ];
            $userAddressModel->setModelArrayData($addressData, $fields);
        } else {
            $addressData['created_user_id'] = $doUserId;
            $addressData['created_user_name'] = $doUserName;
            $addressData['updated_user_id'] = $doUserId;
            $addressData['updated_user_name'] = $doUserName;
        }
        $userAddressModel->setModelData($addressData);


        if (!$userAddressModel instanceof UserAddressModel) {
            return $userAddressModel;
        }
        $flag = $this->userAddressMapper->save($userAddressModel);

        if (is_array($flag)) {
            return $flag;
        } else if (is_int($flag) && $flag > 0) {
            return $userAddressModel;
        } else {
            return ['记录未修改或修改异常'];
        }
    }

    /**
     * @param array $addressData
     * @return array|bool
     */
    protected function checkData(array $addressData)
    {
        $userAddressValidate = new UserAddressValidate();
        return $userAddressValidate->check($addressData,[],'add') ? true : (is_array($userAddressValidate->getError()) ? $userAddressValidate->getError() : [$userAddressValidate->getError()]);

    }


    /**
     * 获取用户默认地址的信息
     * @param int $userId
     * @param int $plateformId
     * @return UserAddressModel|array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDefaultUserAddress(int $userId, int $plateformId)
    {
        return $this->userAddressMapper->getDefaultUserAddress($userId, $plateformId);
    }

    /**
     * 获取用户地址的信息
     * @param int $id
     * @param int $userId
     * @param int $plateformId
     * @return UserAddressModel|array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserAddressById(int $id, int $userId, int $plateformId)
    {
        return $this->userAddressMapper->getUserAddressById($id, $userId, $plateformId);
    }


}