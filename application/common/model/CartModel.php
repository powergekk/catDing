<?php
namespace app\common\model;

class CartModel extends BaseModel
{

    protected $name = "BasicCart";

    /**
     * JSON 输出字段
     * @var array
     */
    protected $visible = [
        'id',
        'plateform_id',
        'goods_sku',
        'goods_name',
        'qty',
        'sale_price',
        'is_check',
        'logo_pic',
        //---- 增加返回字段
        'original_price',
    ];


    /**
     * 设定字段类型
     * @var array
     */
    protected $type = [
        'id' => 'integer',
        'plateform_id' => 'integer',
        'qty' => 'integer',
        'sale_price' => 'float',
        'is_check' => 'integer',
        'is_default' => 'integer',
        //---- 增加返回字段
        'original_price' => 'float',
    ];


}