<?php

namespace app\common\model;

use app\common\consts\YesOrNo;

class HomeModel extends BaseModel
{
    protected $name = "BasicHome";

    /**
     * @var null
     */
    protected $detailList = null;


    /**
     * JSON 输出字段
     * @var array
     */
    protected $visible = [
        'home_no',
        'show_type',
        'detailList'
    ];



    /**
     * 关联模型
     * @return \think\model\relation\HasMany
     */
    public function detailList()
    {
        return $this->hasMany('HomeDetailModel', 'home_id', 'id')
            ->where('del_status', YesOrNo::NO)
            ->order(["rank" => "asc"])
            ->field("*");
    }
}