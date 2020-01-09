<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/13
 * Time: 11:15
 */
namespace app\store\controller;

use utils\PageUtils;
use app\common\service\OrderDetailService;


class Count extends Common
{
    /**
     * @var OrderDetailService
     */
    private $orderDetailService;


    public function _after_instance()
    {
        // TODO: Implement _after_instance() method.
        $this->orderDetailService = OrderDetailService::instance();
    }

    /**
     * 统计总览
     * @return array
     */
    public function index()
    {
        $data['type'] = $this->request->param('data.type/d', 0); //类型：1.昨天指标 2.7天指标 3.15天指标 4.30天指标 5.全部

        try {
            if(empty($data['type']) || !in_array($data['type'], [1, 2, 3, 4, 5]))
            {
                throw new \Exception('指标类型[type]未传或者不合法');
            }
            $page = $this->getPage();
            $pageSize = $this->getPageSize();

            $data['platformId'] = $this->getPlateformId();
            $data['storeNo'] = $this->getStoreUserSession()->getStoreInfo()->getStoreNo();
            if(empty($data['platformId']) || empty($data['storeNo']))
                throw new \Exception('非法用户');

            $res = $this->orderDetailService->getEvaList($data, $page, $pageSize);

            if($res instanceof \Exception)
                throw new \Exception($res->getMessage());

            $ret = PageUtils::formatPageResult($page, $pageSize, isset($res['total'])? $res['total']:0, $res['data']);

            return $this->indexResp->ok($ret, '操作成功')->send();
        } catch (\Exception $exception)
        {
            return $this->catchExcpetion($exception);
        }
    }
}