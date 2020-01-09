<?php

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\consts\TableStatus;
use app\common\model\PlateformUserModel;
use app\common\model\UserRelationModel;
use app\common\model\UserTypeModel;

class PlateformUserMapper extends BaseMapper
{
    use InstanceTrait;

    /**
     * 登录时查询公品用户
     * @param $plateformUser
     * @param $plateformId
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUser($plateformUser, $plateformId){
        $result = PlateformUserModel::where([
            'plateform_user' => $plateformUser,
            'plateform_id' => $plateformId,
            'plateform_status' => TableStatus::EFFECTIVE
        ])->find();
        return $result ? true : false;
    }

}