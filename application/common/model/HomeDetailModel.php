<?php
namespace app\common\model;

class HomeDetailModel extends BaseModel
{
    protected $name = "BasicHomeDetail";


    /**
     * JSON 输出字段
     * @var array
     */
    protected $visible = [
        'show_type',
        'jump_type',
        'show_context',
        'pic_context',
        'jump_context',
        'rank'
    ];



}