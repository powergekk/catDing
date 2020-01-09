<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/8
 * Time: 16:56
 */

namespace app\common\service;


use app\common\base\traits\InstanceTrait;
use app\common\cache\ComCache;
use app\common\mapper\PlateformMapper;

class PlateformService
{
    use InstanceTrait;

    /**
     * @var PlateformMapper
     */
    private $plateformMapper;

    /**
     * 实例化后调用的方法,用于注入
     */
    protected function _after_instance()
    {
        $this->plateformMapper = PlateformMapper::instance();
    }


    /**
     * 获取平台信息(缓存)
     * @param int $plateformId
     * @return \app\common\model\PlateformModel|array|false|mixed|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getCacheModelById(int $plateformId)
    {
        $cacheKey = $this->getCacheKey($plateformId);
        if (ComCache::has($cacheKey)) {
            $plateformModel = ComCache::get($cacheKey);
        } else {
            $plateformModel = $this->plateformMapper->getById($plateformId);
            if (!empty($plateformModel)) {
                ComCache::set($cacheKey, $plateformModel, 3600);
            }
        }
        return $plateformModel;

    }


    protected function getCacheKey(int $plateformId)
    {
        return "plateform:" . $plateformId;
    }

}