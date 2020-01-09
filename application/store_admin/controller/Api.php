<?php

namespace app\store_admin\controller;

use app\common\consts\UserType;
use app\common\model\TokenModel;
use app\common\model\UserModel;
use app\common\service\SmsService;
use app\common\service\UserService;
use app\index\controller\User;
use app\store\base\IndexResp;
use app\store_admin\dto\RequestDto;
use app\store_admin\dto\ResponseDto;
use app\store_admin\dto\store\StoreRequestDto;
use think\Request;
use think\Response;
use utils\QxHttpClient;
use utils\ReflectionUtils;


class Api extends Common
{


    protected function _after_instance(...$args)
    {
        // TODO: Implement _after_instance() method.
    }


    public function index(){
        /** @var $data RequestDto ; */
        /** @var $response ResponseDto ; */
        /** @var $user User ; */
//        $user = $this->session('user');
//        echo realpath(dirname(__DIR__).'/../../');die;
        //判断是否登录
//        session('token', time());

        $token = $this->session('token');

        try {
            $data = ReflectionUtils::arrayToObj($this->request->param(), RequestDto::class); //判断请求参数是否缺省

            if ((empty($token) || is_null($token)) && strpos($data->getServer(), 'login') === false) //未登录情况
            {
                //提示请求需要登录
                return $this->response->err('请先登录！', ResponseDto::CODE_NO_LOGIN, [], $data->getServer());
            } elseif(strpos($data->getServer(), 'login') !== false) { //登录请求
                //设置请求参数
                $requestData = new StoreRequestDto;
                $requestData->setData($data->getData());
//                $requestData->setToken($token);
                $requestData->setPlateformId($data->getPlateformId());
                $requestData->setVersion('1.0');

            } else //其他请求
            {
                $requestData = new StoreRequestDto;
                $requestData->setData($data->getData());
                $requestData->setToken($token);
                $requestData->setPlateformId($data->getPlateformId());
                $requestData->setVersion('1.0');
            }

            $url = $this->routeByServer($data->getServer());

            /** 处理请求*/
            $httpClient = new QxHttpClient();
            $httpClient->setUrl($url); //设置请求url
            $httpClient->setOptions([
                CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8']
            ]);
            $httpClient->setMethod(QxHttpClient::METHOD_POST); //设置请求方式


            $httpClient->setData($requestData); //设置请求参数

            $httpResponse = $httpClient->getHttpResponse();

            $retData = json_decode($httpResponse->getBody(), true);
            //dump($retData);die;
            $ret = ResponseDto::instance();
            if (empty($retData) || !is_array($retData)) {
//TODO 异常处理
                return $ret->responseError('返回值不能为空且必须是数组！', $data->getServer());
            } else {
                //验证是否返回正常，是否有带token
                if (isset($retData['data']) && is_array($retData['data'])) {
                    if (isset($retData['data']['token'])) {
                        $token = $retData['data']['token'];
                        $this->session('token', $token); //设置token

                        $ret->setALl($httpResponse, $data->getServer()); //设置所有返回值
                        return $ret;
                    } else {
                        //TODO 没有返回token的情况
                        return $ret->setALl($httpResponse, $data->getServer());
                    }
                } else {
                    //TODO 没有data 异常处理
                    return $ret->dataError('data缺失！', $data->getServer());
                }

            }

        }catch (\Exception $exception)
        {
            $ret = ResponseDto::instance();
            return $ret->exception($exception);
        }
    }

    private function routeByServer($server)
    {
        $baseRoot = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

        $baseRoot = substr($baseRoot, 0, strpos($baseRoot, 'public') + 6);
        //根据server地址分配路由
        $url = $baseRoot. '/store.php'. $server;
        return $url;
    }

    
}