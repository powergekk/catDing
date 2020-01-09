<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/6
 * Time: 19:45
 */

namespace app\common\base;


abstract class JsonSerializableAbstract implements \JsonSerializable
{

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return json_encode($this->toArray());
    }


    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }


    public function __toString()
    {
        return $this->jsonSerialize();
    }



}