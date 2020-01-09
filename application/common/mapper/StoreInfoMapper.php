<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/26
 * Time: 15:42
 */

namespace app\common\mapper;

use app\common\consts\OrderStatus;
use app\common\model\StoreInfoModel;
use app\common\model\StoreOrderModel;
use app\common\service\StoreInfoService;
use traits\think\Instance;

class StoreInfoMapper extends BaseMapper
{

    use Instance;

    /**
     * 通过用户ID查询对应的对象
     * @param int $userId
     * @return array|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfoByUserId(int $userId)
    {
        return StoreInfoModel::where(['user_id' => $userId])->find();
    }

    /**
     * 按店铺编号编辑店铺信息
     * @param array $params
     * @param string $storeNo
     * @return false|int
     */
    public function editStoreByStoreNo(array $params, string $storeNo)
    {
        $storeInfoModel = new StoreInfoModel();
        $res = $storeInfoModel->save($params, ['store_no' => $storeNo]);

        return $res;
    }

}