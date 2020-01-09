<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 11:04
 */

namespace app\common\base;


use app\common\cache\ComCache;
use app\index\cache\CacheBean;
use think\Config;

interface BaseUserSession
{


    /**
     * 登陆方式：no(未登陆)
     */
    const LOGIN_no = 'no';

    /**
     * 登陆方式：微信
     */
    const LOGIN_wechat = 'wechat';

    /**
     * 登陆方式：tel
     */
    const LOGIN_tel = 'tel';

    /**
     * 登录方式： sms
     */
    const LOGIN_sms = "sms";

    /**
     * 登陆方式：account
     */
    const LOGIN_account = 'account';






    /**
     * @return string
     */
    public function getToken():string;



    /**
     * @return int
     */
    public function getId(): int;



    /**
     * @return string
     */
    public function getAccount(): string;



    /**
     * @return string
     */
    public function getNickName(): string;



    /**
     * @return string
     */
    public function getTel(): string;


    /**
     * @return string
     */
    public function getEmail(): string;


    /**
     * @return int
     */
    public function getPlateformId(): int;


    /**
     * @return array
     */
    public function getInfo(): array;



    /**
     * @return string
     */
    public function getLoginType(): string;




    static public function instance();



    /**
     * 批量赋值
     * @param array $data
     * @return $this
     */
    public function dataSet(array $data);



    /**
     * 获取info里的值
     * @param null $name
     * @return array|mixed|null
     */
    public function getInfoVal($name = null);


    /**
     * 验证是否登陆
     * @return bool
     */
    public function isLogin():bool;



    /**
     * 初始化TOKEN
     * @param string $token
     * @return bool
     */
    public function startByToken(string $token):bool;



    /**
     * @return bool
     */
    public function writeToSession():bool;


    /**
     * 清除登陆session
     * @return bool
     */
    public function clearSession(): bool;

}