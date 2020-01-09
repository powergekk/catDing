<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 16:43
 */

namespace app\common\service;


use app\common\base\traits\InstanceTrait;
use app\common\consts\InOut;
use app\common\consts\OrderLogType;
use app\common\consts\YesOrNo;
use app\common\entity\database\OrderLogDto;
use app\common\mapper\OrderLogMapper;

class OrderLogService extends ServiceAbstract
{
    use InstanceTrait;

    /**
     * @var OrderLogMapper
     */
    private $orderLogMapper;


    protected function _after_instance()
    {
        $this->orderLogMapper = OrderLogMapper::instance();
    }


    /**
     * 创建订单日志
     * @param string $orderNo
     * @param string $storeOrderNo
     * @param int $doUserId
     * @param string $doUserName
     * @param string $orderLogType
     * @param null $data
     * @return false|int
     */
    public function createOrderLog(string $orderNo, string $storeOrderNo = '', int $doUserId, string $doUserName, string $orderLogType, $data=null)
    {
        $orderLogDto = new OrderLogDto();
        $orderLogDto->setOrderNo($orderNo);
        $orderLogDto->setStoreOrderNo($storeOrderNo);
        $orderLogDto->setDelDtatus(YesOrNo::NO);
        $orderLogDto->setCreatedUserId($doUserId);
        $orderLogDto->setCreatedUserName($doUserName);
        $orderLogDto->setUpdatedUserId(0);
        $orderLogDto->setUpdatedUserName('');
        $orderLogDto->setLogType($orderLogType);


        switch($orderLogType){
            case OrderLogType::LOGISTIC:
                $orderLogDto->setOutContent($data);
                $orderLogDto->setInContent($data);
                $orderLogDto->setShowType(InOut::BOTH);
                break;


            case OrderLogType::ORDER_CREATE:
                $orderLogDto->setOutContent('创建订单');
                $orderLogDto->setInContent('创建订单:['.$doUserName.']');
                $orderLogDto->setShowType(InOut::BOTH);
                break;

            case OrderLogType::ORDER_MODIFY:
                $orderLogDto->setOutContent('订单修改');
                $orderLogDto->setInContent('订单修改:['.$doUserName.'], data:['.json_encode($data).']');
                $orderLogDto->setShowType(InOut::BOTH);
                break;

            case OrderLogType::ORDER_STATUS:
                $orderLogDto->setOutContent('订单状态:['.$data.']');
                $orderLogDto->setInContent('订单修改:['.$doUserName.'], 状态:['.$data.']');
                $orderLogDto->setShowType(InOut::BOTH);
                break;

            default:
                user_error('只支持 OrderLogType 的方法');
                break;
        }

        return $this->orderLogMapper->save($orderLogDto);

    }


}