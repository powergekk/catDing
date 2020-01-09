<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 18:41
 */

namespace app\common\mapper;


use app\common\base\traits\InstanceTrait;
use app\common\entity\database\OrderLogDto;
use app\common\model\OrderLogModel;

class OrderLogMapper extends BaseMapper
{
    use InstanceTrait;


    /**
     * @param OrderLogDto $orderLogDto
     * @return false|int
     */
    public function save(OrderLogDto $orderLogDto)
    {


        $orderLogModel = new OrderLogModel();

        $orderLogModel->setAttr('order_no', $orderLogDto->getOrderNo());
        $orderLogModel->setAttr('store_order_no', $orderLogDto->getStoreOrderNo());
        $orderLogModel->setAttr('out_content', $orderLogDto->getOutContent());
        $orderLogModel->setAttr('in_content', $orderLogDto->getInContent());
        $orderLogModel->setAttr('show_type', $orderLogDto->getShowType());
        $orderLogModel->setAttr('created_user_id', $orderLogDto->getCreatedUserId());
        $orderLogModel->setAttr('created_user_name', $orderLogDto->getCreatedUserName());
        $orderLogModel->setAttr('updated_user_id', $orderLogDto->getUpdatedUserId());
        $orderLogModel->setAttr('updated_user_name', $orderLogDto->getUpdatedUserName());
        $orderLogModel->setAttr('del_status', $orderLogDto->getDelDtatus());
        $orderLogModel->setAttr('log_type', $orderLogDto->getLogType());

        return $orderLogModel->save();
    }


}