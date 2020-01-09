<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 15:25
 */

namespace app\common\model;


use app\common\consts\TableStatus;

class GoodsModel extends BaseModel
{
    protected  $name= "basic_goods";

    /*
     * 销量
     */
    public function orderDetails()
    {
        return $this->hasOne('OrderDetailsModel','goods_sku','sku');
    }
    /**
     * 判断当前商品是否有效
     * @return bool
     */
    public function isEffective()
    {
        if(!isset($this->status) || !isset($this->zhpt_status) || !isset($this->third_status)){
            return false;
        }
        if($this->status == TableStatus::EFFECTIVE && $this->zhpt_status == TableStatus::EFFECTIVE && $this->third_status == TableStatus::EFFECTIVE){
            return true;
        }else{
            return false;
        }
    }
}