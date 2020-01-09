<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/8
 * Time: 16:58
 */

namespace app\common\model;

use app\common\consts\TableStatus;


/**
 * 平台模型
 * Class PlateformModel
 * @package app\common\model
 */
class PlateformModel extends BaseModel
{
    protected $name = "BasicPlateform";


    /**
     * JSON 输出字段
     * @var array
     */
    protected $visible = [
        'name',
        'plateform_type',
        'status',
        'expire_time',
    ];


    /**
     * 判断是否有效
     * @return bool
     */
    public function isEffective(): bool
    {
        if ($this->getAttrVal("del_status") != 'N' || $this->getAttrVal("status") != TableStatus::EFFECTIVE) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * 是否可访问的平台
     * @return bool
     */
    public function isWebPlateform()
    {
        if ($this->getAttrVal("plateform_type") == 1) {
            return true;
        } else {
            return false;
        }
    }
}