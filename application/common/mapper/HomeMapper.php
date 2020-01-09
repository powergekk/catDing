<?php

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\consts\DelStatus;
use app\common\model\HomeModel;


class HomeMapper extends BaseMapper
{
    use InstanceTrait;

    /**
     * 查询一条有效的记录
     * @param string $homeNo 首页位置编号
     * @param int $plateformId 平台ID
     * @param string $time 查询时间
     * @return array|false|\PDOStatement|string|\think\Model|HomeModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getEffectiveItem(string $homeNo, int $plateformId, string $time)
    {
        $condition = $this->createEffectiveWhere([
            'home_no' => $homeNo,
            'plateform_id' => $plateformId,
            'beign_time' => ['elt', $time],
            'end_time' => ['egt', $time]
        ]);
        $order = ["level_num" => "desc"];
        return HomeModel::where($condition)->order($order)->find();
    }
}