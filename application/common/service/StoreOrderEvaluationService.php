<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/25
 * Time: 11:06
 */

namespace app\common\service;

use app\common\base\traits\InstanceTrait;
use app\common\mapper\StoreOrderEvaluationMapper;

class StoreOrderEvaluationService{
    use InstanceTrait;
    /**
     * @var GoodsEvaluationMapper
     */
    private $storeOrderEvaluationMapper;

    public function _after_instance(){
        $this->storeOrderEvaluationMapper = StoreOrderEvaluationMapper::instance();
    }
}