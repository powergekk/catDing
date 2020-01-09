<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 14:51
 */

namespace app\common\model;

use app\common\consts\YesOrNo;

class UserAddressModel extends BaseModel
{
    protected $name = "AccountUserAddress";


    /**
     * JSON 输出字段
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'tel',
        'province',
        'province_id',
        'city',
        'city_id',
        'area',
        'area_id',
        'is_default',
        'mark',
        'address',
        'latitude',
        'longitude',
        'tude_type'
    ];

    /**
     * 设定字段类型
     * @var array
     */
    protected $type = [
        'plateform_id' => 'integer',
        'province_id' => 'integer',
        'city_id' => 'integer',
        'area_id' => 'integer',
        'is_default' => 'integer',
        'user_id' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float'
    ];


    /**
     * 生成当前记录的hashCode
     * @return string
     */
    public function createHashCode()
    {
        //如果已删除，生成随机code
        if (YesOrNo::YES === $this->getAttrVal("del_status")) {
            return uniqid(rand(0, 9999999));
        }
        $codeArr = [];

        $fields = [
            "del_status",
            "user_id",
            "name",
            "tel",
            "province",
            "province_id",
            "city",
            "city_id",
            "area",
            "area_id",
            "address",
            'latitude',
            'longitude',
            'tude_type',
        ];

        sort($fields);

        foreach ($fields as $f) {
            $codeArr[] = $this->getAttrVal($f);
        }

        $this->hash_code = md5(str_replace(" ", "", implode("|", $codeArr)));

        return $this->hash_code;
    }


}