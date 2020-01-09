<?php

namespace app\index\controller;

use app\common\service\AreaService;

class Area extends Common
{


    /**
     * 不需要验证登陆,*表示不验证登陆
     * @var array|*
     */
    protected $notCheckLoginAction = '*';


    /**
     * @var areaService
     */
    private $areaService;

    /**
     * @return mixed|void 实例化后调用方法
     */
    protected function _after_instance()
    {
        $this->areaService = AreaService::instance();
    }

    /**
     * @return array  获得地址的下级地址结果集
     */
    public function getChildrenList()
    {
        $id = $this->getPostInputData("id", 0, 'intval');
        try {
            $list = $this->areaService->getChildList($id);

            return $this->indexResp->ok($list)->send();
        } catch (\Exception $e) {
            return $this->indexResp->err($e->getMessage())->send();
        }
    }

    /**
     * @return array  获得地址的下级地址结果集
     */
    public function getProvinceList()
    {
        try {
            $list = $this->areaService->getProvinceList();
            return $this->indexResp->ok($list)->send();
        } catch (\Exception $e) {
            return $this->indexResp->err($e->getMessage())->send();
        }
    }

}