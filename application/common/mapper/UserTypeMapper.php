<?php

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\consts\TableStatus;
use app\common\model\UserRelationModel;
use app\common\model\UserTypeModel;

class UserTypeMapper extends BaseMapper
{
    use InstanceTrait;

    public function getGpList(){
        return UserTypeModel::where(['type_name' => '公品用户'])->field('type_no')->select();
    }

}