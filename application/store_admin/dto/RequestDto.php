<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/29
 * Time: 16:02
 */

namespace app\store_admin\dto;


/**
 * 外界与中间代理层之间的request请求协议
 * Class RequestDto
 * @package app\store_admin\dto
 * @require
 */
class RequestDto
{

    /**
     * @#name 服务名
     * @require
     * @var string
     */
    private $server='';

    /**
     * @#name 平台ID
     * @var int
     * @require
     */
    private $plateformId = 0;


    /**
     * @#name 数据
     * #var array
     * @require
     */
    private $data = [];


    /**
     * @return string
     */
    public function getServer(): string
    {
        return $this->server;
    }

    /**
     * @param string $server
     */
    public function setServer(string $server): void
    {
        $this->server = $server;
    }

    /**
     * @return int
     */
    public function getPlateformId(): int
    {
        return $this->plateformId;
    }

    /**
     * @param int $plateformId
     */
    public function setPlateformId(int $plateformId): void
    {
        $this->plateformId = $plateformId;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function toArray(){
        return get_object_vars($this);
    }


}