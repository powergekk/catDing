<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 11:13
 */
namespace app\common\model;

use app\common\consts\DelStatus;
use app\common\consts\TableStatus;

class StoreGoodsRelationModel extends BaseModel{
    protected $name = "StoreGoodsRelation";



    /**
     * JSON 输出字段
     * @var array
     */
    protected $visible = [
        'plateform_id',
        'goods_sku',
        'sku',
        'name',
        'up_down_status',
        'short',
        'desc',
        'logo_pic',
        'unit_id',
        'unit_name',
        'brand_id',
        'brand_name',
        'category_id',
        'category_name',
        'spec',
        'attr',
        'market_price',
        'original_price',
        'zhpt_goods_sku',
        'zhpt_status',
        'store_qty',
        'sale_price',
        'purchase_price',


        //---- 增加返回字段

    ];


    /**
     * 设定字段类型
     * @var array
     */
    protected $type = [
        'plateform_id' => 'integer',
        'store_qty' => 'integer',
        'sale_price' => 'float',
        'unit_id' => 'integer',
        'brand_id' => 'integer',
        'category_id' => 'integer',
        'market_price' => 'float',
        'original_price' => 'float',
        'store_qty' => 'integer',
        'purchase_price' => 'float',
    ];


    /**
     * 判断当前商品是否有效
     * @return bool
     */
    public function isEffective(){
        if (isset($this->status) && $this->status = TableStatus::EFFECTIVE){
            return true;
        }else{
            return false;
        }
    }
}