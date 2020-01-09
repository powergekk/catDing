<?php

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\consts\TableStatus;
use app\common\model\UserModel;

class UserMapper extends BaseMapper
{
    use InstanceTrait;

    /**
     *
     * @param int $id
     * @return \app\common\model\UserModel|NULL
     */
    public function getUserInfoById(int $id)
    {
        return UserModel::get($id);
    }

    /**
     *
     * @param array $condition
     * @param int $page
     * @param int $pageSize
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserList(array $condition, int $page = 1, int $pageSize = 10)
    {
        return UserModel::where($condition)->order("")
            ->limit(($page - 1) * $pageSize, $pageSize)
            ->select();
    }

    protected function formatCondition(array $condition): array
    {
        return $condition;
    }

    /**
     * 查询用户信息(account)
     * @param string $account
     * @param int $plateformId 登陆平台
     * @return array|false|\PDOStatement|string|\think\Model|UserModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserByAccount(string $account)
    {
        return UserModel::where([
            'account' => $account,
            'status' => TableStatus::EFFECTIVE
        ])->find();
    }

    /**
     * 查询用户信息(tel)
     * @param string $tel
     * @param int $plateformId
     * @return array|false|\PDOStatement|string|\think\Model|UserModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserByTel(string $tel)
    {
        return UserModel::where([
            'tel' => $tel,
            'status' => TableStatus::EFFECTIVE
        ])->find();
    }


    /**
     * 修改密码
     * @param int $id
     * @param string $newPassword
     * @param int $plateformId
     * @return bool
     */
    public function changePassword(int $id, int $plateformId, string $newPassword, string $salt): bool
    {
        return UserModel::where([
            'id' => $id,
            'plateform_id' => $plateformId,
            'status' => TableStatus::EFFECTIVE
        ])->Update([
            'password' => $newPassword,
            'salt' => $salt
        ]) > 0 ? true : false;
    }

    /**
     * 根据ID获取密码值和salt值
     * @param int $id
     * @param int $plateformId
     * @param string $password
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getPassword(int $id, int $plateformId, string $password){
        $result = UserModel::where([
            'id' => $id,
            'plateform_id' => $plateformId,
            'status' => TableStatus::EFFECTIVE
        ])->find();

        return $result;
    }

}