<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/11
 * Time: 14:53
 */

namespace app\index\controller;

use app\common\model\UserAddressModel;
use app\common\service\UserAddressService;

class UserAddress extends Common
{

    /**
     * 不需要验证登陆,*表示不验证登陆
     * @var array|*
     */
    protected $notCheckLoginAction = [];


    /**
     * @var userAddressService
     */
    private $userAddressService;

    /**
     * @return mixed|void 实例化后调用方法
     */
    protected function _after_instance()
    {
        $this->userAddressService = UserAddressService::instance();
    }

    /**
     * @return array
     */
    public function getUserAddressList()
    {
        try {
            $userId = $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            //默认不分页
            $page = 1;
            $pageSize = 9999;
            $list = $this->userAddressService->getAddressListById($userId, $plateformId, $page, $pageSize);
            return $this->indexResp->ok(['list' => $list])->send();
        } catch (\Exception $e) {
            return $this->indexResp->err($e->getMessage())->send();
        }
    }


    /**
     * 设置用户的收货地址为默认/非默认
     * @return array
     */
    public function setDefault()
    {
        try {
            $userId = $this->getUserId();
            $userName = $this->user->getNickName();
            $plateformId = $this->getPlateformId();
            $id = $this->getPostInputData('id', 0, 'intval');
            $isDefault = $this->getPostInputData('is_default', false, 'boolval');
//            var_export($isDefault);exit;
            //判断记录ID
            if ($id < 1) {
                return $this->indexResp->err("[id]不合法!")->send();
            }
            $result = $this->userAddressService->setDefault( $id, $userId, $plateformId,$userId, $userName, $isDefault);
            if ($result) {
                return $this->indexResp->ok()->send();
            } else {
                return $this->indexResp->err("记录不存在或已处理!")->send();
            }
        } catch (\Exception $e) {
            return $this->indexResp->err($e->getMessage())->send();
        }
    }


    /**
     * 删除用户的收货地址
     * @return array
     */
    public function del()
    {
        try {
            $userId = $this->getUserId();
            $userName = $this->user->getNickName();
            $plateformId = $this->getPlateformId();
            $id = $this->getPostInputData('id', 0, 'intval');

            //判断记录ID
            if ($id < 1) {
                return $this->indexResp->err("[id]不合法!")->send();
            }
            $result = $this->userAddressService->del($id, $userId, $plateformId, $userId, $userName);

            if ($result) {
                return $this->indexResp->ok()->send();
            } else {
                return $this->indexResp->err("记录不存在或已处理!")->send();
            }
        } catch (\Exception $e) {
            return $this->indexResp->err($e->getMessage())->send();

        }
    }

    /**
     * 添加|修改用户的收货地址
     * @return array
     */
    public function saveUserAddress()
    {
        try {

            $userId = $this->getUserId();
            $userName = $this->user->getNickName();
            $plateformId = $this->getPlateformId();
            $data = $this->getPostInputData();
            $id = $this->getPostInputData('id', 0, 'intval');
            if ($id > 0) {
                $result = $this->userAddressService->updateUserAddress( $id, $userId, $plateformId, $data, $userId, $userName);
            } else {
                $result = $this->userAddressService->addUserAddress($userId, $plateformId, $data, $userId, $userName);
            }
            if (is_array($result)) {
                return $this->indexResp->err(implode(",", $result))->send();
            } elseif ($result instanceof UserAddressModel) {
                return $this->indexResp->ok($result)->send();
            } else {
                return $this->indexResp->err("写入异常!")->send();
            }

        } catch (\Exception $e) {
            return $this->indexResp->err($e->getMessage())->send();

        }
    }


    /**
     * 获取用户的ID地址信息
     * @return array
     */
    public function getInfo(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $id = $this->getPostInputData('id', 0, 'intval');

            //判断ID
            if($id < 1){
                return $this->indexResp->err('地址ID不合法!')->send();
            }
            //
            $data = $this->userAddressService->getUserAddressById($id, $userId, $plateformId);
            if(empty($data)){
                return $this->indexResp->err('未查询到ID:['.$id.']的记录!')->send();
            }else{
                return $this->indexResp->ok($data)->send();
            }
        }catch (\Exception $e){
            return $this->indexResp->err($e->getMessage())->send();
        }
    }


    /**
     * 获取用户的ID地址信息
     * @return array
     */
    public function getDefaultInfo(){
        try{
            $userId = $this->getUserId();
            $plateformId = $this->getPlateformId();

            //
            $data = $this->userAddressService->getDefaultUserAddress($userId, $plateformId);
            if(empty($data)){
                return $this->indexResp->err('当前用户还未添加地址信息')->send();
            }else{
                return $this->indexResp->ok($data)->send();
            }
        }catch (\Exception $e){
            return $this->indexResp->err($e->getMessage())->send();
        }
    }



}