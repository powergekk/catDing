<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/30
 * Time: 11:14
 */
return [
    '/' => 'store/product/index', //店铺商品列表
    'operate' => 'store/product/operate', //新增，修改商品
    'model' => 'store/product/model', //模板选择列表
    'commit' => 'store/product/commit', //模板生成商品提交
    'change' => 'store/product/change', //调整库存和供价
    'sale' => 'store/product/sale', //上下架
    'platform' => 'store/product/platform', //平台sku管理列表
    'mark' => 'store/product/mark' //关联sku
];