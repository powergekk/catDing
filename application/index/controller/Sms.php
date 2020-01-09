<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/16
 * Time: 12:04
 */

namespace app\index\controller;


use app\common\service\SmsService;

class Sms extends Common
{

    /**
     * 不需要验证登陆,*表示不验证登陆
     * @var array|*
     */
    protected $notCheckLoginAction = [
        'send',
        'verify'
    ];


    /**
     *
     * @var SmsService
     */
    private $smsService;

    /**
     * 实例化后调用方法
     * @return mixed
     */
    protected function _after_instance()
    {
//        //验证参数
//        $this->checkPost();
        $this->smsService = SmsService::instance();
    }


    public function send()
    {
        try {
            $tel = $this->getPostInputData('tel', '', 'trim');
            $type = $this->getPostInputData('type', '', 'trim');
            //TODO 判断验证参数
            $flag = $this->smsService->send($tel, $type);
            if ($flag !== true) {
                return $this->indexResp->err(implode(",", $flag))->send();
            } else {
                return $this->indexResp->ok()->send();
            }


        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }

    }

    public function verify()
    {
        try {
            $tel = $this->getPostInputData('tel', '', 'trim');
            $type = $this->getPostInputData('type', '', 'trim');
            $code = $this->getPostInputData('code', '', 'trim');
            if ($this->smsService->auditCode($tel, $type, $code)) {
                return $this->indexResp->ok()->send();
            } else {
                return $this->indexResp->err("验证码错误")->send();
            }
        } catch (\Exception $e) {
            return $this->catchExcpetion($e);
        }
    }
}