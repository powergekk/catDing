<?php

namespace app\index\controller;

use app\common\service\HomeService;
use app\index\base\IndexResp;
use app\index\bean\UserSession;

class Home extends Common
{


    /**
     * @var HomeService
     */
    private $homeService;


    /**
     * 实例化后调用方法
     * @return mixed
     */
    protected function _after_instance()
    {
        $this->homeService = HomeService::instance();
    }

    /**
     * 页面内容查询
     * @return array
     */
    public function index()//: IndexResp
    {
        try{
            $homeNos = $this->getPostInputData('homeNos', []);
            $plateformId = $this->getPlateformId();
            $data = $this->homeService->getHomeInfos($homeNos, $plateformId);
            return $this->indexResp->ok($data)->send();
        }catch (\Exception $e){
            //日志记录
            return $this->indexResp->err($e->getMessage())->send();
        }

    }


}
