<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 17:34
 */

namespace app\common\validate;


/**
 * Class UserAddressValidate
 * @package app\common\validate
 */
class UserAddressValidate extends BaseValidate
{

    // 批量验证
    protected $batch = true;


    protected $rule = [
        'plateform_id' => 'number',
        'user_id' => 'number',
        'name' => 'require|chs|max:25',
        'tel' => 'require|length:11',
        'province_id' => 'require|number',
        'city_id' => 'require|number',
        'area_id' => 'require|number',
        'address' => 'require|max: 150',
        'is_default' => 'number|in:0,1',
        'mark' => 'max: 20',
        'latitude' => 'require|number',
        'longitude' => 'require|number',
        'tude_type' => 'in:tencent,baidu,kailide,gaode,google'
    ];

    protected $message = [
        'plateform_id' => '所属平台不能为空',
        'user_id' => '用户ID必需为纯数字',
        'name.require' => '收货人名不能为空',
        'name.chs' => '收货人必需为纯中文',
        'name.max' => '收货人名不得超过25字',
        'tel.require' => '收货人电话不能为空',
        'tel.length' => '收货人电话长度必需为11位',
        'province_id' => '省份不能为空',
        'city_id' => '城市不能为空',
        'area_id' => '区不能为空',
        'address.require' => '详细地址不能为空',
        'address.length' => '详细地址不得超过150字',
        'is_default' => '是否默认状态不能为空',
//        'mark.require' => '标签不能为空',
        'mark.max' => '标签长度不能超过20字',
        'latitude.require' => '纬度不能为空',
        'latitude.number' => '纬度必须为纯数字',
        'longitude.require' => '经度不能为空',
        'longitude.number' => '经度必须为纯数字',
        'tude_type' => '类型不在范围内'
    ];

    protected $scene =
        [
            "add"=>['plateform_id','user_id','name','tel','province_id','city_id','area_id','is_default','address','mark','tude_type']
        ];
}