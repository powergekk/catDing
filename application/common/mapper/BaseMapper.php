<?php

namespace app\common\mapper;

use app\common\consts\YesOrNo;

abstract class BaseMapper
{
//    use InstanceTrait;


    /**
     * 生成查询的WHERE条件
     * @param array $map
     * @return array
     */
    protected function createEffectiveWhere(array $map)
    {
        $map['del_status'] = YesOrNo::NO;
        return $map;
    }


    /**
     * 创建删除状态数组
     * @param array $data
     * @return array
     */
    protected function createDelStatusData(array $data){
        $data['del_status'] = YesOrNo::YES;
        return $data;
    }
}