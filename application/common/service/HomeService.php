<?php

namespace app\common\service;

use app\common\base\traits\InstanceTrait;
use app\common\mapper\AreaMapper;
use app\common\mapper\HomeMapper;

class HomeService extends ServiceAbstract
{
    use InstanceTrait;

    /**
     * @var HomeMapper
     */
    private $homeMapper;

    /**
     * 实例化后调用函数
     */
    protected function _after_instance()
    {
        $this->homeMapper = HomeMapper::instance();
    }


    /**
     *
     * @param array $homeNos
     * @param int $plateformId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getHomeInfos(array $homeNos, int $plateformId)
    {
        $list = [];
        foreach ($homeNos as $no) {
            $homeModel = $this->homeMapper->getEffectiveItem($no, $plateformId, date('Y-m-d H:i:s'));
            if(empty($homeModel)){
                $list[$no] = new \stdClass();
            }else{
                //触发详情查询
                $homeModel->detailList;
                //TODO 处理内容
                $list[$no] = $homeModel;
            }

        }
        return $list;

    }


}