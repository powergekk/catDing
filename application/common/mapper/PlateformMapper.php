<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/8
 * Time: 16:57
 */

namespace app\common\mapper;


use app\common\base\traits\InstanceTrait;
use app\common\model\PlateformModel;
use app\common\model\PlateformUserModel;

/**
 * Class PlateformMapper
 * @package app\common\mapper
 */
class PlateformMapper extends BaseMapper
{
    use InstanceTrait;


    /**
     * @param int $plateformId
     * @return array|false|\PDOStatement|string|\think\Model|PlateformModel
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getById(int $plateformId)
    {
        $condition = $this->createEffectiveWhere(['id' => $plateformId]);
        return PlateformModel::where($condition)->find();
    }

    /**
     * 验证登录平台
     * @param int $plateformId
     * @param string $plateformNo
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkPlateform(int $plateformId, string $plateformNo){
        $condition = $this->createEffectiveWhere([
            'id' => $plateformId,
            'plateform_no' => $plateformNo
        ]);
        return PlateformModel::where($condition)->find();
    }
}