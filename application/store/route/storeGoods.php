<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/19
 * Time: 16:00
 */
return [
    'list' => 'store/StoreGoods/getUserGoodsList',
    'up/list' => 'store/StoreGoods/getUpGoodsList',
    'down/list' => 'store/StoreGoods/getDownGoodsList',
    'get/down' => 'store/StoreGoods/getStoreGoodsDown',
    'get/up' => 'store/StoreGoods/getStoreGoodsUp',
    'goods/add' => 'store/StoreGoods/getGoodsToAdd',//查询能够添加的商品
    'change/price' => 'store/StoreGoods/changePrice',
    'change/qty' => 'store/StoreGoods/changeQty',
    'add/goods' => 'store/StoreGoods/addGoods',
];