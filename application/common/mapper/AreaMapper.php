<?php

namespace app\common\mapper;

use app\common\base\traits\InstanceTrait;
use app\common\model\AreaModel;

class AreaMapper extends BaseMapper
{
    use InstanceTrait;

    /**
     * 通过id获取下级地区结果集
     * @param int $id
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListById(int $id)
    {
        return AreaModel::where(['parent_id' => $id])->select();
    }


    /**
     * 通过id获取下级地区结果集
     * @param int $level
     * @return false|\PDOStatement|string|\think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getListByLevel(int $level)
    {
        return AreaModel::where(['area_level' => $level])->select();
    }


    /**
     * 通过地区名[与地区等级]查询信息
     * @param string $name
     * @param int $level
     * @param int $parentId
     * @return array|false|\PDOStatement|string|\think\Model|AreaModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfoByName(string $name, int $level = -1, int $parentId = -1)
    {
        $condition = [
            'area_name' => $name,
        ];
        if ($level > 0) {
            $condition['area_level'] = $level;
        }
        if($parentId >= 0){
            $condition['parent_id'] = $parentId;
        }
        return AreaModel::where($condition)->find();
    }



    /**
     * 通过地区ID查询信息
     * @param int $id
     * @param int $level
     * @param int $parentId
     * @return array|false|\PDOStatement|string|\think\Model|AreaModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfoById(int $id, int $level = -1, int $parentId = -1)
    {
        $condition = [
            'id' => $id,
        ];
        return AreaModel::where($condition)->find();
    }
}