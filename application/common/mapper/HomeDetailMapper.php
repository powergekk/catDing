<?php

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\consts\DelStatus;
use app\common\model\HomeDetailModel;
use app\common\model\HomeModel;


class HomeDetailMapper extends BaseMapper
{
    use InstanceTrait;


    /**
     * 查询ID的详情
     * @param int $homeId
     * @return array|false|\PDOStatement|string|\think\Model|HomeDetailModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function selectItems(int $homeId)
    {
        $condition = $this->createEffectiveWhere(['home_id' => $homeId,]);
        $order = ["rank" => "asc"];
        return HomeDetailModel::where($condition)->order($order)->select();
    }
}