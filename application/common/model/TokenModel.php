<?php
namespace app\common\model;

class TokenModel extends BaseModel
{

    protected $name = "AccountToken";

    /**
     * JSON 输出字段
     * @var array
     */
    protected $visible = [
        'token_type',
        'access_token',
        'login_type',
        'status',
        'expire_time',
    ];

}