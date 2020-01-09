<?php

namespace app\store\controller;

use app\common\consts\UserType;
use app\common\model\TokenModel;
use app\common\model\UserModel;
use app\common\service\SmsService;
use app\common\service\StoreInfoService;
use app\common\service\UserService;
use app\store\base\IndexResp;


class User extends Common
{


    /**
     * 不需要验证登陆,*表示不验证登陆
     * @var array|*
     */
    protected $notCheckLoginAction = [
        'login',
        'smssend',
        'smslogin'
    ];


    /**
     *
     * @var UserService
     */
    private $userService;

    /**
     * @var smsService
     */
    private $smsService;


    /**
     * @var StoreInfoService
     */
    private $storeInfoService;


    /**
     * 实例化后调用方法
     * @return mixed
     */
    protected function _after_instance()
    {
        $this->userService = UserService::instance();
        $this->smsService = SmsService::instance();
        $this->storeInfoService = StoreInfoService::instance();
    }


    /**
     * @return array
     */
    public function getSelfInfo(): array
    {
        try {
            $id = $this->getUserId();
            $user = $this->userService->getUserById($id);
            return $this->indexResp->ok($user->toArray())->send();
        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }

    }

    /**
     * 获取用户结果集
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserList(): array
    {
        try {
            $id = $this->getUserId();
            $user = $this->userService->getUserList(array());
            return $this->indexResp->ok($user)->send();
        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }
    }


    /**
     * 登陆
     * account: string , password: string, plateformId: int
     * @return array
     */
    public function login()
    {
        try {
            $account = $this->getPostInputData('account', '', 'trim');
            $pwd = $this->getPostInputData('password', '', 'trim');
            $plateformId = $this->getPlateformId();
            if (trim($account) === '' || $pwd === '' || $plateformId < 1) {
                return $this->indexResp->err("账号、密码与平台不能为空")->send();
            }
            $data = $this->userService->login($account, $pwd, $plateformId, UserType::STORE);
            if (empty($data)) {
                return $this->indexResp->err("账号或密码异常")->send();
            } else {
                return $this->loginSuccess($data['user'], $data['token']);
            }
        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }

    }

    /**
     * 短信登录发送短信
     * @return array
     */
    public function smsSend()
    {
        try {
            $tel = $this->getPostInputData('tel', '', 'trim');
            $type = $this->getPostInputData('type', '', 'trim');

            $flag = $this->smsService->send($tel, $type);
            if ($flag !== true) {
                return $this->indexResp->err(implode(',', $flag))->send();
            } else {
                return $this->indexResp->ok()->send();
            }
        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }
    }

    /**
     * 短信验证登录
     * @return array
     */
    public function smsLogin()
    {
        try {
            $tel = $this->getPostInputData('tel', '', 'trim');
            $type = $this->getPostInputData('type', '', 'trim');
            $code = $this->getPostInputData('code', '', 'trim');
            $plateformId = $this->getPlateformId();

            if ($this->smsService->auditCode($tel, $type, $code)) {
                $data = $this->userService->smsLogin($tel, $plateformId, UserType::STORE);
                if (empty($data)) {
                    return $this->indexResp->err("账号异常")->send();
                } else {
                    return $this->loginSuccess($data['user'], $data['token']);
                }
            } else {
                return $this->indexResp->err("验证码出错")->send();
            }
        } catch (\Exception $e) {
            $this->catchExcpetion($e);
        }
    }


    /**
     * 用户登录成功后的动作
     * @param UserModel $user
     * @param TokenModel $tokenModel
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    protected function loginSuccess(UserModel $user, TokenModel $tokenModel)
    {
        try {
            //判断用户类型
            if ($user->getAttr('user_type') !== UserType::STORE) {
                return $this->indexResp->err("用户类型异常")->send();
            }
            $token = $tokenModel->getAttr('access_token');
            $this->user->setToken($token);
            $this->user->setLoginType($tokenModel->getAttr('login_type'));

            //组装用户信息数组
            $userData = $user->toArray();
            $userData['plateform_id'] = IndexResp::instance()->getPlateformId();
            $userData['id'] = $user->getAttr('id');
            $userData['login_type'] = $user->getAttr('login_type');
            //查询便利店 TODO
            $storeInfo = $this->storeInfoService->getInfoByUserId($user->getAttr('id'));
            if(empty($storeInfo)){
                return $this->indexResp->err("用户类型异常")->send();
            }
            $userData['store_info'] = $storeInfo->toArray();

            $this->user->dataSet($userData);
            $this->user->writeToSession();
            return $this->indexResp->ok([
                'token' => $token,
                'login_type' => $this->user->getLoginType(),
                'nick_name' => $this->user->getNickName(),
                'tel' => $this->user->getTel(),
                'logo_pic' => $this->user->getInfoVal('logo_pic')
            ])->send();
        } catch (\Exception $e) {var_export($e);
            return $this->catchExcpetion($e);
        }
    }


    /**
     * 修改密码
     * @return array
     */
    public function changePassword()
    {
        try {
            $id = $this->getUserId();
            $plateformId = $this->getPlateformId();
            $password = $this->getPostInputData('password');
            $newPassword = $this->getPostInputData('newPassword');

            if (!$this->userService->confirmPassword($id, $plateformId, $password)) {
                return $this->indexResp->err("原密码输入错误")->send();
            }

            $result = $this->userService->changePassword($id, $plateformId, $newPassword);
            if ($result === true) {
                return $this->indexResp->ok()->send();
            } else {
                return $this->indexResp->err("修改密码失败!")->send();
            }

        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }
    }
}