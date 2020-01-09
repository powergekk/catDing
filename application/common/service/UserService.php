<?php

namespace app\common\service;

use app\common\base\traits\InstanceTrait;
use app\common\consts\TableStatus;
use app\common\consts\TokenType;
use app\common\consts\UserRelationType;
use app\common\consts\UserType;
use app\common\consts\YesOrNo;
use app\common\mapper\PlateformMapper;
use app\common\mapper\PlateformUserMapper;
use app\common\mapper\UserMapper;
use app\common\mapper\UserRelationMapper;
use app\common\mapper\UserTypeMapper;
use app\common\model\AccountTokenModel;
use app\common\model\TokenModel;
use app\common\model\UserModel;
use app\index\bean\UserSession;
use utils\ReflectionUtils;

class UserService extends ServiceAbstract
{
    use InstanceTrait;

    /**
     *
     * @var UserMapper
     */
    private $userMapper;

    /**
     * @var  UserRelationMapper
     */
    private $userRelationMapper;

    /**
     * @var UserTypeMapper
     */
    private $userTypeMapper;

    /**
     * @var PlateformUserMapper
     */
    private $plateformUserMapper;

    /**
     * @var PlateformMapper
     */
    private $plateformMapper;

    /**
     * 实例化后调用的方法,用于注入
     */
    protected function _after_instance()
    {
        $this->userMapper = UserMapper::instance();
        $this->userRelationMapper = UserRelationMapper::instance();
        $this->userTypeMapper = UserTypeMapper::instance();
        $this->plateformUserMapper = PlateformUserMapper::instance();
        $this->plateformMapper = PlateformMapper::instance();
    }


    /**
     * @param int $id
     * @return UserModel|array|NULL
     */
    public function getUserById(int $id)
    {
        $user = $this->userMapper->getUserInfoById($id);
        if ($user instanceof UserModel) {
            return $user;
        }
        return array();
    }

    /**
     * 获取用户列表
     *
     * @param array $condition
     * @param int $page
     * @param int $pageSize
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserList(array $condition, int $page = 1, int $pageSize = 10): array
    {
        $list = $this->userMapper->getUserList($condition, $page, $pageSize);
        if (is_array($list)) {
            return $list;
        } else {
            return array(
                $list
            );
        }
    }

    /**
     * 验证公品用户类型
     * @param int $userId
     * @param string $type
     * @return bool
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkUserType(int $userId, string $type){
        $relationList = $this->getUserRelationList($userId);
        return isset($relationList[UserRelationType::TYPE]) && in_array($type, $relationList[UserRelationType::TYPE]);
    }


    /**
     * 登陆
     * @param string $account 用户名|手机号
     * @param string $pwd 密码
     * @param int $plateformId 平台
     * @param string $flag
     * @return array|bool|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function login(string $account, string $pwd = '', int $plateformId, string $flag){
        if (preg_match('/^\d{11}$/', $account)) {
            //手机登陆
            $loginType = UserSession::LOGIN_tel;
            $user = $this->userMapper->getUserByTel($account);
        } else {
            //账号登陆
            $loginType = UserSession::LOGIN_account;
            $user = $this->userMapper->getUserByAccount($account);
        }

        if (empty($user)) {
            return false;
        }
        if ($user->password != $this->pwd($pwd, $user->salt)) {
            return false;
        }

        $list = $this->userRelationMapper->getLoginlist($user->id);

        $user->user_type = $flag;
        $typeCheck = $this->checkUserType($user->id, $flag);

        if (!$typeCheck){
            return null;
        }

        $result = $this->checkPlateform($list, $plateformId);
        if (!$result){
            return null;
        };
        if ($flag == UserType::CUSTOMER){
            foreach ($list as $value){
                if ($value['relation_type'] == 'plateform_user'){
                    if (!$this->plateformUserMapper->getUser($value['type_value'], $plateformId)){
                        return null;
                    }
                }
            }
        }

        $user->login_type = $loginType;

        return $this->setToken($user);
    }

    /**
     * 登录时验证平台
     * @param array $list
     * @param int $plateformId
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkPlateform(array $list, int $plateformId){
        foreach ($list as $value){
            if ($value['relation_type'] == 'plateform'){
                $result = $this->plateformMapper->checkPlateform($plateformId, $value['type_value']);
                if ($result){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 短信验证登录
     * @param string $tel
     * @param int $plateformId
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function smsLogin(string $tel, int $plateformId, $flag)
    {
        $user = $this->userMapper->getUserByTel($tel, $plateformId);

        if (empty($user)) {
            return false;
        }

        $list = $this->userRelationMapper->getLoginlist($user->id);

        $user->user_type = $flag;
        $typeCheck = $this->checkUserType($user->id, $flag);
        if (!$typeCheck){
            return null;
        }

        if (!$typeCheck){
            return null;
        }
        $result = $this->checkPlateform($list, $plateformId);
        if (!$result){
            return null;
        };
        if ($flag == UserType::CUSTOMER){
            foreach ($list as $value){
                if ($value['relation_type'] == 'plateform_user'){
                    if (!$this->plateformUserMapper->getUser($value['type_value'], $plateformId)){
                        return null;
                    }
                }
            }
        }

        $user->login_type = UserSession::LOGIN_sms;

        return $this->setToken($user);
    }

    /**
     * 生成Token
     * @param UserModel $user
     * @return array
     */
    public function setToken(UserModel $user){
        $token = sha1($user->getAttr('id') . uniqid()) . '-' . uniqid();

        $TokenModel = new TokenModel();
        $TokenModel->user_id = $user->getAttr('id');
        $TokenModel->token_type = TokenType::LOGIN;
        $TokenModel->login_type = $user->getAttr('login_type');
        $TokenModel->access_token = $token;
        $TokenModel->status = TableStatus::EFFECTIVE;
        $TokenModel->created_at = date('Y-m-d H:i:s');
        $TokenModel->created_user_id = $user->getAttr('id');
        $TokenModel->created_user_name = $user->getAttr('nick_name');
        $TokenModel->updated_user_id = 0;
        $TokenModel->updated_user_name = '';
        $TokenModel->del_status = YesOrNo::NO;

        $TokenModel->save();
        return [
            'user' => $user,
            'token' => $TokenModel
        ];
    }

    /**
     * 用户的密码加密
     * @param string $pwd
     * @param string $salt
     * @return string
     */
    protected function pwd(string $pwd, string $salt)
    {
        return md5(md5($pwd) . $salt);
    }

    /**
     * 核对原密码
     * @param int $id
     * @param int $plateformId
     * @param string $password
     */
    public function confirmPassword(int $id, int $plateformId, string $password){
        $result = $this->userMapper->getPassword($id, $plateformId, $password);
        //var_export($password['password']);die;
        $salt = $result['salt'];
        $oriPassword = $result['password'];
        $password = $this->pwd($password, $salt);
        return $password == $oriPassword ? true : false;
    }

    /**
     * 修改密码
     * @param int $id
     * @param int $plateformId
     * @param string $newPassword
     * @param string $salt
     */
    public function changePassword(int $id, int $plateformId, string $newPassword)
    {
        $salt = $this->createSalt();
        $newPassword = $this->pwd($newPassword, $salt);
        return $this->userMapper->changePassword($id, $plateformId, $newPassword, $salt);
    }

    /**
     * 生成salt
     */
    public function createSalt(){
        return uniqid();
    }


    /**
     * 获取用户所有的关联数据
     * @param int $userId
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getUserRelationList(int $userId){
        $keyConstsList = ReflectionUtils::getClassConstantList(UserRelationType::class);
        $list = [];
        foreach($keyConstsList as $keyConsts){
            $key = $keyConsts[ReflectionUtils::DEF_VALUE];
            $data = $this->userRelationMapper->getTypeListData($userId, $key);
            if(!empty($data)){
                $list[$key] = array_column($data, 'type_value');
            }else{
                $list[$key] = [];
            }
        }
        return $list;
    }

    /**
     * @param int $userId
     * @param string $key
     * @return string
     */
    protected function getUserRelationCacheKey(int $userId, string $key){
        return 'relation:'.$userId.':'.$key;
    }


}