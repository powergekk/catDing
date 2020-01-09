<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 15:04
 */

namespace app\common\model;


class OrderDetailsModel extends BaseModel
{
    protected  $name= "StoreOrderDetails";

    protected $insert = ['created_at'];
    protected $update = ['updated_at'];

    protected function setCreated_atAttr()
    {
        return date('YmdHis');
    }
    protected function setUpdated_atAttr()
    {
        return date('YmdHis');
    }

    public function goods()
    {
        return $this->belongsTo('GoodsModel','sku','goods_sku');
    }


}