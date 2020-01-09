<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 12:05
 */

namespace app\common\service;


use app\common\base\traits\InstanceTrait;
use app\common\consts\StringTime;
use app\index\cache\CacheBean;

class SmsService extends ServiceAbstract
{
    use InstanceTrait;


    /**
     * @var CacheBean
     */
    private $cache;

    protected function _after_instance()
    {
        $this->cache = CacheBean::instance();
    }

    /**
     * 是否允许发送
     * @param string $tel
     * @param string $type
     * @param string $ticket
     * @return array|bool
     */
    public function checkRequest(string $tel, string $type, string $ticket = '')
    {
        return true;
        return ['当前号码不允许发送'];
    }


    /**
     * 发送短信并写入缓存
     * @param string $tel
     * @param string $type
     * @param array $data
     * @return array|bool
     */
    public function send(string $tel, string $type, array $data = [])
    {
        $code = str_pad(rand(0, 999999), 6, 0, STR_PAD_LEFT);
        //TODO 调 发短信接口
        $check = $this->checkRequest($tel, $type, '');
        if($check !== true){
            return $check;
        }
        $this->createCodeCache($tel, $type, $code);
        return true;
    }


    /**
     * 写入缓存
     * @param string $tel
     * @param string $type
     * @param string $code
     * @return bool
     */
    protected function createCodeCache(string $tel, string $type, string $code)
    {
        $key = $this->getCodeCacheKey($tel, $type);
        return $this->cache->set($key, $code, StringTime::_5_MINUTES);
    }

    /**
     * 获取缓存code
     * @param string $tel
     * @param string $type
     * @return mixed
     */
    protected function getCodeCache(string $tel, string $type)
    {
        $key = $this->getCodeCacheKey($tel, $type);
        return $this->cache->get($key, null);

    }

    /**
     * 获取缓存KEY
     * @param string $tel
     * @param string $type
     * @return string
     */
    protected function getCodeCacheKey(string $tel, string $type)
    {
        return "SMS:" . $type . ":" . $tel;
    }


    /**
     * 验证code
     * @param string $tel
     * @param string $type
     * @param string $code
     * @return bool
     */
    public function auditCode(string $tel, string $type, string $code)
    {
        $oldCode = $this->getCodeCache($tel, $type);
        $oldCode = "123";
        if(is_null($oldCode)){
            return false;
        }elseif($code == $oldCode){
            return true;
        }else{
            return false;
        }
    }

}