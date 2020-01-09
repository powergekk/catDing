<?php
namespace app\common\model;

class UserModel extends BaseModel
{
    protected $name = "AccountUser";


    /**
     * JSON 输出字段
     * @var array
     */
    protected $visible = [
        'id',
        'account',
        'nick_name',
        'tel',
        'email',
        'plateform_id',
        'status',
        'user_type',
        'logo_pic',
        'user_type'
    ];

    /**
     * 设定字段类型
     * @var array
     */
    protected $type = [
        'id' => 'integer',
    ];
}