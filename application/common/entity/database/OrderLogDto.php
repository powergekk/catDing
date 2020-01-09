<?php
namespace app\common\entity\database;
use app\common\base\JsonSerializableAbstract;
use app\common\consts\DelStatus;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/31
 * Time: 16:55
 */

/**
 * @name 订单日志的对象
 * Class OrderLogDto
 * @package app\common\entity\database
 */
final class OrderLogDto extends JsonSerializableAbstract
{


    /**
     * @var int
     */
    protected $id=0;


    /**
     * @name 订单号
     * @var string
     */
    protected $orderNo;


    /**
     * @name 便利店订单号
     * @var string
     */
    protected $storeOrderNo;


    /**
     * @name 对外显示
     * @var string
     */
    protected $outContent;


    /**
     * @name 内部显示内容
     * @var string
     */
    protected $inContent;


    /**
     * @name 内容显示类型
     * in:只内部展现，out:只外部展现, both:内外都显示
     * @var string
     */
    protected $showType;


    /**
     * @name 创建用户id
     * @var int
     */
    protected $createdUserId;


    /**
     * @name 创建用户名称
     * @var string
     */
    protected $createdUserName;


    /**
     * @name 更新用户id
     * @var int
     */
    protected $updatedUserId;


    /**
     * @name 更新用户名称
     * @var string
     */
    protected $updatedUserName;


    /**
     * @name 删除状态
     * @var DelStatus
     */
    protected $delDtatus;


    /**
     * @name 记录类型
     * logistic:物流记录,order-status:订单状态变更,order-modify:订单修改
     * @var string
     */
    protected $logType;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getOrderNo()
    {
        return $this->orderNo;
    }

    /**
     * @param string $orderNo
     */
    public function setOrderNo(string $orderNo): void
    {
        $this->orderNo = $orderNo;
    }

    /**
     * @return string
     */
    public function getStoreOrderNo()
    {
        return $this->storeOrderNo;
    }

    /**
     * @param string $storeOrderNo
     */
    public function setStoreOrderNo(string $storeOrderNo): void
    {
        $this->storeOrderNo = $storeOrderNo;
    }

    /**
     * @return string
     */
    public function getOutContent()
    {
        return $this->outContent;
    }

    /**
     * @param string $outContent
     */
    public function setOutContent(string $outContent): void
    {
        $this->outContent = $outContent;
    }

    /**
     * @return string
     */
    public function getInContent()
    {
        return $this->inContent;
    }

    /**
     * @param string $inContent
     */
    public function setInContent(string $inContent): void
    {
        $this->inContent = $inContent;
    }

    /**
     * @return string
     */
    public function getShowType()
    {
        return $this->showType;
    }

    /**
     * @param string $showType
     */
    public function setShowType(string $showType): void
    {
        $this->showType = $showType;
    }

    /**
     * @return string
     */
    public function getCreatedUserId()
    {
        return $this->createdUserId;
    }

    /**
     * @param string $createdUserId
     */
    public function setCreatedUserId($createdUserId): void
    {
        $this->createdUserId = $createdUserId;
    }

    /**
     * @return string
     */
    public function getCreatedUserName()
    {
        return $this->createdUserName;
    }

    /**
     * @param string $createdUserName
     */
    public function setCreatedUserName(string $createdUserName): void
    {
        $this->createdUserName = $createdUserName;
    }

    /**
     * @return string
     */
    public function getUpdatedUserId()
    {
        return $this->updatedUserId;
    }

    /**
     * @param string $updatedUserId
     */
    public function setUpdatedUserId($updatedUserId): void
    {
        $this->updatedUserId = $updatedUserId;
    }

    /**
     * @return string
     */
    public function getUpdatedUserName()
    {
        return $this->updatedUserName;
    }

    /**
     * @param string $updatedUserName
     */
    public function setUpdatedUserName(string $updatedUserName): void
    {
        $this->updatedUserName = $updatedUserName;
    }

    /**
     * @return string
     */
    public function getDelDtatus()
    {
        return $this->delDtatus;
    }

    /**
     * @param string $delDtatus
     */
    public function setDelDtatus(string $delDtatus): void
    {
        $this->delDtatus = $delDtatus;
    }

    /**
     * @return string
     */
    public function getLogType()
    {
        return $this->logType;
    }

    /**
     * @param string $logType
     */
    public function setLogType(string $logType): void
    {
        $this->logType = $logType;
    }


}