<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 15:36
 */

namespace app\common\service;

use app\common\base\traits\InstanceTrait;
use app\common\mapper\StoreInfoMapper;

class StoreInfoService extends ServiceAbstract
{
    use InstanceTrait;

    /**
     * @var StoreInfoMapper
     */
    protected $storeInfoMapper;


    protected function _after_instance()
    {
        $this->storeInfoMapper = StoreInfoMapper::instance();
    }


    /**
     * @#name 通过用户ID查询对应的对象
     * @param int $userId
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfoByUserId(int $userId)
    {
        return $this->storeInfoMapper->getInfoByUserId($userId);
    }

    /**
     * 按店铺编号编辑店铺信息
     * @param $objOrArr array|object
     * @param string $storeNo
     * @return false|int
     * @throws \Exception
     */
    public function editStoreByStoreNo($objOrArr, string $storeNo)
    {
        if(empty($storeNo)) throw new \Exception('店铺编号不能为空');

        $param['updated_at'] = date('Y-m-d H:i:s'); //需要编辑的内容
        if(is_array($objOrArr))
        {
            $param['province'] = $objOrArr['provinceName'];
            $param['province_id'] = $objOrArr['provinceId'];
            $param['city'] = $objOrArr['cityName'];
            $param['city_id'] = $objOrArr['cityId'];
            $param['area'] = $objOrArr['AreaName'];
            $param['area_id'] = $objOrArr['areaId'];
            $param['address'] = $objOrArr['address'];
            $param['connect_number'] = $objOrArr['tel'];

            $res = $this->storeInfoMapper->editStoreByStoreNo($param, $storeNo);
            return $res;
        } elseif (is_object($objOrArr))
        {
            $param['province'] = $objOrArr->getProvinceName();
            $param['province_id'] = $objOrArr->getProvinceId();
            $param['city'] = $objOrArr->getCityName();
            $param['city_id'] = $objOrArr->getCityId();
            $param['area'] = $objOrArr->getAreaName();
            $param['area_id'] = $objOrArr->getAreaId();
            $param['address'] = $objOrArr->getAddress();
            $param['connect_number'] = $objOrArr->getTel();

            $res = $this->storeInfoMapper->editStoreByStoreNo($param, $storeNo);
            return $res;
        } else
        {
            throw new \Exception('必须是数组或者对象');
        }
    }
}