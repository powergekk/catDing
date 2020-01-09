<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/29
 * Time: 16:52
 */

namespace app\store_admin\dto\store;


use utils\ArrayUtils;

/**
 * 中间代理层与内部api之间的request请求协议
 * Class StoreRequestDto
 * @package app\store_admin\dto\store
 */
class StoreRequestDto implements \JsonSerializable
{
    /**
     * @#name token
     * @require
     * @var string
     */
    private $token = '';

    /**
     * @#name plateformId
     * @var int
     * @require
     */
    private $plateformId = 0;

    /**
     * @#name plateformId
     * @var string
     * @require
     */
    private $version = '1.0';

    /**
     * @#name 请求数据
     * @var null
     */
    private $data = null;

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getPlateformId(): int
    {
        return $this->plateformId;
    }

    /**
     * @param int $platformId
     */
    public function setPlateformId(int $plateformId): void
    {
        $this->plateformId = $plateformId;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null $data
     */
    public function setData($data): void
    {
        if(is_array($data) && empty($data)){
            $data = new \stdClass();
        }elseif(is_array($data) && ArrayUtils::isAssocArray($data)){
            user_error('not allow index array!');
        }elseif(is_object($data)  || is_array($data)){

        }else{
            user_error('not must be a object or key relation array!');
        }
        $this->data = $data;
    }



    public function __construct()
    {
        $this->setData(new \stdClass());
    }


    public function toArray(){
        return get_object_vars($this);
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        // TODO: Implement jsonSerialize() method.
        return json_encode($this->toArray());
    }
}