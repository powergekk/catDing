<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:04
 */

namespace app\store\bean;


use app\common\base\BaseUserSession;
use app\common\entity\database\StoreInfoDto;
use app\index\cache\CacheBean;
use think\Config;
use utils\ReflectionUtils;

final class UserSession implements BaseUserSession
{

    static protected $_instance = null;



    /**
     * @#name 用户token
     * @var string
     */
    protected $token;


    /**
     * @#name 用户ID
     * @var int
     */
    protected $id;


    /**
     * @#name 账号
     * @var string
     */
    protected $account;

    /**
     * @#name 用户名
     * @var string
     */
    protected $nick_name;


    /**
     * @#name 手机号码
     * @var string
     */
    protected $tel;


    /**
     * @#name email
     * @var string
     */
    protected $email;


    /**
     * @#name 平台
     * @var int
     */
    protected $plateform_id;


    /**
     * @#name 用户类型
     * @var string
     */
    protected $user_type;


    /**
     * @#name 用户信息
     * @var array
     */
    protected $info = [];


    /**
     * @#name 登陆状态
     * ，no,wechat,tel,account
     * @var string
     */
    protected $login_type = self::LOGIN_no;


    /**
     * @var StoreInfoDto
     */
    protected $store_info;



    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAccount(): string
    {
        return $this->account;
    }

    /**
     * @param string $account
     */
    public function setAccount(string $account): void
    {
        $this->account = $account;
    }

    /**
     * @return string
     */
    public function getNickName(): string
    {
        return $this->nick_name;
    }

    /**
     * @param string $nick_name
     */
    public function setNickName(string $nick_name): void
    {
        $this->nick_name = $nick_name;
    }

    /**
     * @return string
     */
    public function getTel(): string
    {
        return $this->tel;
    }

    /**
     * @param string $tel
     */
    public function setTel(string $tel): void
    {
        $this->tel = $tel;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getPlateformId(): int
    {
        return $this->plateform_id;
    }

    /**
     * @param int $plateform_id
     */
    public function setPlateformId(int $plateform_id): void
    {
        $this->plateform_id = $plateform_id;
    }

    /**
     * @return string
     */
    public function getUserType(): string
    {
        return $this->user_type;
    }

    /**
     * @param string $user_type
     */
    public function setUserType(string $user_type): void
    {
        $this->user_type = $user_type;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * @param array $info
     */
    public function setInfo(array $info): void
    {
        $this->info = $info;
    }

    /**
     * @return string
     */
    public function getLoginType(): string
    {
        return $this->login_type;
    }

    /**
     * @param string $login_type
     */
    public function setLoginType(string $login_type): void
    {
        $this->login_type = $login_type;
    }

    /**
     * @return StoreInfoDto
     */
    public function getStoreInfo(): StoreInfoDto
    {
        return $this->store_info;
    }

    /**
     * @param StoreInfoDto $store_info
     */
    public function setStoreInfo(StoreInfoDto $store_info): void
    {
        $this->store_info = $store_info;
    }



    /**
     * UserSession constructor.
     */
    protected function __construct()
    {

    }


    public function __sleep()
    {
        user_error("response can not serialize");
    }

    public function __clone()
    {
        user_error("response can not clone");
    }


    static public function instance()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


    /**
     * 批量赋值
     * @param array $data
     * @return $this|BaseUserSession
     * @throws \ReflectionException
     */
    public function dataSet(array $data)
    {
        $arr = get_object_vars($this);
        foreach ($arr as $key => $val) {
            if (isset($data[$key])) {
                if($key == 'store_info'){
                    $this->$key = ReflectionUtils::arrayToObj($data[$key], StoreInfoDto::class);
                }else{
                    $this->$key = $data[$key];
                }
            }
        }
        $this->setInfo($data);
        return $this;
    }


    /**
     * 获取info里的值
     * @param null $name
     * @return array|mixed|null
     */
    public function getInfoVal($name = null)
    {
        if (is_null($name)) {
            return $this->info;
        } else {
            return isset($this->info[$name]) ? $this->info[$name] : null;
        }
    }


    /**
     * 验证是否登陆
     * @return bool
     */
    public function isLogin(): bool
    {
        return self::LOGIN_no != $this->getLoginType();
    }


    /**
     * 初始化TOKEN
     * @param string $token
     * @return bool
     */
    public function startByToken(string $token): bool
    {
        if (!empty($this->token)) {
            user_error("token must empty");
        }
        $key = $this->getSessionKey($token);
        if ($this->getCacheBean()->has($key)) {
            $data = $this->getCacheBean()->get($key);
            if (empty($data)) {
                return false;
            } else {
                $this->getCacheBean()->set($key, $data, Config::get('token_time'));
                $this->setToken($token);
                $this->dataSet($data);
                return true;
            }
        } else {
            return false;
        }
    }


    /**
     * @return bool
     */
    public function writeToSession(): bool
    {
        if (empty($this->token)) {
            user_error("token must not empty");
        } elseif (!$this->isLogin()) {
            user_error("must login");
        }
        $key = $this->getSessionKey($this->token);
        return $this->getCacheBean()->set($key, $this->getInfo(), Config::get('token_time'));
    }


    /**
     * 清除登陆session
     * @return bool
     */
    public function clearSession(): bool
    {
        if (empty($this->token)) {
            return true;
        } else {
            $key = $this->getSessionKey($this->getToken());
            if ($this->getCacheBean()->has($key)) {
                //重新格式化
                self::$_instance = null;
                return $this->getCacheBean()->clear($key);
            } else {
                return true;
            }
        }

    }

    /**
     * 缓存KEY
     * @param string $token
     * @return string
     */
    protected function getSessionKey(string $token)
    {
        return "user:login:" . BIND_MODULE . ":" . $token;
    }


    /**
     * 获取缓存实例
     * @return \app\common\base\traits\InstanceTrait|static|CacheBean
     */
    protected function getCacheBean()
    {
        return CacheBean::instance();
    }
}