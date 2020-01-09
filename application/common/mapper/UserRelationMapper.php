<?php

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\consts\TableStatus;
use app\common\model\UserRelationModel;

class UserRelationMapper extends BaseMapper
{
    use InstanceTrait;

    public function getLoginList(int $id){
        return UserRelationModel::where([
            'user_id' => $id,
            'type_status' => TableStatus::EFFECTIVE
        ])->field('relation_type,type_value')->select();
    }

    /**
     * 获取用户的关联列表
     * @param int $userId
     * @param string $type
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getTypeListData(int $userId, string $type){
        $condition = ['user_id'=>$userId, 'relation_type'=>$type];
        return UserRelationModel::where($this->createEffectiveWhere($condition))->select();
    }

}