<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 14:53
 */

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\consts\DelStatus;
use app\common\model\UserAddressModel;


class UserAddressMapper extends BaseMapper
{
    use InstanceTrait;


    /**
     * 获取用户在当前平台的收货地址列表
     * @param int $userId 用户ID
     * @param int $plateformId 平台ID
     * @param int $page
     * @param int $pageSize
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAddressListById(int $userId, int $plateformId, int $page = 1, int $pageSize = 10)
    {
        $condition = $this->createEffectiveWhere([
            'user_id' => $userId,
            'plateform_id' => $plateformId
        ]);
        $order = ['is_default' => 'desc', 'updated_at' => 'desc'];
        return UserAddressModel::where($condition)->order($order)
            ->page($page)
            ->limit($pageSize)
            ->select();
    }


    /**
     * 获取用户在当前平台的收货地址
     * @param int $id 地址ID
     * @param int $userId 用户ID
     * @param int $plateformId 平台ID
     * @return array|false|\PDOStatement|string|\think\Model|UserAddressModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAddressById(int $id, int $userId, int $plateformId)
    {
        $condition = $this->createEffectiveWhere([
            'id' => $id,
            'user_id' => $userId,
            'plateform_id' => $plateformId
        ]);
        return UserAddressModel::where($condition)->find();
    }

    /**
     * 保存与修改
     * @param UserAddressModel $userAddressModel
     * @return array|false|int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function save(UserAddressModel $userAddressModel)
    {
        $checkExistsModel = [];
        if ($userAddressModel->getAttrVal("id") > 0) {
            if (isset($userAddressModel->created_at)) {
                unset($userAddressModel->created_at);
            }
            if (isset($userAddressModel->created_user_id)) {
                unset($userAddressModel->created_user_id);
            }
            if (isset($userAddressModel->created_user_name)) {
                unset($userAddressModel->created_user_name);
            }
            $checkExistsModel['id'] = ['neq', $userAddressModel->getAttrVal("id")];
        }
        //验证是否有冲突
        $hashCode = $userAddressModel->createHashCode();
        $checkExistsModel['hash_code'] = $hashCode;
        $one = UserAddressModel::where($checkExistsModel)->field('id')->find();
        if (!empty($one)) {
            return ["记录重复，请不要提交重复的内容"];
        }
        //
        $flag = $userAddressModel->save();
        if ($flag > 0) {
            //插入成功，处理默认
            if ($userAddressModel->getAttrVal("is_default") == 1) {
                $id = $userAddressModel->getAttrVal("id");
                $userId = $userAddressModel->getAttrVal("user_id");
                $plateformId = $userAddressModel->getAttrVal("plateform_id");
                $doUserId = intval($userAddressModel->getAttrVal('updated_user_id'));
                $doUserName = $userAddressModel->getAttrVal('updated_user_name');

                $this->setOtherNotDefaultAddress($id, $userId, $plateformId, $doUserId, $doUserName);
            }
        }
        return $flag;
    }


    /**
     * 逻辑删除记录
     * @param int $id 记录ID
     * @param int $userId 用户ID
     * @param int $plateformId 平台ID
     * @param int $doUserId 操作者ID
     * @param string $doUserName 操作者名字
     * @return bool
     */
    public function delAddress(int $id, int $userId, int $plateformId, int $doUserId, string $doUserName)
    {
        $condition = $this->createEffectiveWhere([
            'id' => $id,
            'user_id' => $userId,
            'plateform_id' => $plateformId
        ]);
        $data = [
            'updated_user_id' => $doUserId,
            'updated_user_name' => $doUserName,
        ];
        return UserAddressModel::where($condition)->update($this->createDelStatusData($data)) > 0 ? true : false;
    }


    /**
     * 设置为默认
     * @param int $id 记录ID
     * @param int $userId 用户ID
     * @param int $plateformId 平台ID
     * @param int $doUserId 操作者ID
     * @param string $doUserName 操作者名字
     * @return bool
     */
    public function setDefaultAddress(int $id, int $userId, int $plateformId, int $doUserId, string $doUserName, bool $isDefault = true)
    {
        $condition = $this->createEffectiveWhere([
            'id' => $id,
            'user_id' => $userId,
            'plateform_id' => $plateformId
        ]);
        $data = [
            'is_default' => $isDefault,
            'updated_user_id' => $doUserId,
            'updated_user_name' => $doUserName,
        ];
        return UserAddressModel::where($condition)->update($data) > 0 ? true : false;
    }

    /**
     * 设置其它地址为非默认
     * @param int $id 记录ID
     * @param int $userId 用户ID
     * @param int $plateformId 平台ID
     * @param int $doUserId 操作者ID
     * @param string $doUserName 操作者名字
     * @return bool
     */
    public function setOtherNotDefaultAddress(int $id, int $userId, int $plateformId, int $doUserId, string $doUserName)
    {
        $condition = $this->createEffectiveWhere([
            'id' => ['neq', $id],
            'user_id' => $userId,
            'plateform_id' => $plateformId,
            'is_default' => 1
        ]);
        $data = [
            'is_default' => 0,
            'updated_user_id' => $doUserId,
            'updated_user_name' => $doUserName,
        ];
        return UserAddressModel::where($condition)->update($data) > 0 ? true : false;
    }


    /**
     * 获取用户地址的信息
     * @param int $id
     * @param int $userId
     * @param int $plateformId
     * @return array|false|\PDOStatement|string|\think\Model|UserAddressModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserAddressById(int $id, int $userId, int $plateformId)
    {
        $condition = $this->createEffectiveWhere([
            'id' => $id,
            'user_id' => $userId,
            'plateform_id' => $plateformId
        ]);
        return UserAddressModel::where($condition)->find();
    }


    /**
     * 获取用户默认地址的信息
     * @param int $userId
     * @param int $plateformId
     * @return array|false|\PDOStatement|string|\think\Model|UserAddressModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getDefaultUserAddress(int $userId, int $plateformId)
    {
        $condition = $this->createEffectiveWhere([
            'user_id' => $userId,
            'plateform_id' => $plateformId
        ]);
        $order = ['is_default' => 'desc', 'updated_at' => 'desc'];
        return UserAddressModel::where($condition)->order($order)->find();
    }
}