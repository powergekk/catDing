<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/25
 * Time: 16:30
 */

namespace app\index\behavior;


use app\common\cache\ComCache;
use app\common\exceptions\PlateformRuntimeException;
use app\common\service\PlateformService;
use app\index\base\IndexResp;
use app\index\bean\UserSession;
use http\Exception\RuntimeException;
use think\Config;
use think\exception\HttpResponseException;
use think\Response;


/**
 *
 * Class ApiAuthBehavior
 * @package app\index\behavior
 */
class ApiAuthBehavior
{

    /**
     * 需要验证的字段
     * @var array
     */
    protected $validField = [
        "token",
//        "server_id",
//        "parent_id",
//        "version",
        "time",
        "plateformId"
    ];


    /**
     * @return bool
     */
    public function run()
    {
        try{
            $plateformId = request()->param('plateformId', 0, 'intval');
            $version = request()->param('version', '');
            IndexResp::instance()->setVersion($version);
            //验证平台是否正确
            if (!$this->checkWebPlateformId($plateformId)) {
                throw new PlateformRuntimeException('平台异常');
            }
            IndexResp::instance()->setPlateformId($plateformId);
            //验证登陆
            $this->validSign();
            return true;
        }catch(PlateformRuntimeException $e){
            $response = Response::create(IndexResp::instance()->exception($e)->send(), 'json');
            throw new HttpResponseException($response);
        }

    }


    /**
     * 处理
     * @return bool
     */
    protected function validSign()
    {
        $token = request()->param('token');
        $plateformId = request()->param('plateformId', 0, 'intval');
        if (!empty($token)) {
            $user = UserSession::instance();
            $user->startByToken($token);
            if ($user->isLogin()) {
                if ($plateformId != $user->getPlateformId()) {
                    $user->clearSession();
                    $user = UserSession::instance();
                    $user->setPlateformId($plateformId);
                    return false;
                } else {
                    return true;
                }
            } else {
                $user->setPlateformId($plateformId);
                return false;
            }
        } else {
            UserSession::instance()->setPlateformId($plateformId);
            return false;
        }
    }


    /**
     * 验证是否有字段缺失
     * @param array $data
     * @return array
     */
    protected function checkParam(array $data): array
    {
        $errField = [];
        foreach ($this->validField as $field) {
            if (!isset($data[$field])) {
                $errField[] = $field;
            }
        }
        return $errField;
    }


    /**
     * @param int $plateformId
     * @return bool
     * @throws \think\exception\DbException
     */
    protected function checkWebPlateformId(int $plateformId): bool
    {
        $plateformModel = PlateformService::instance()->getCacheModelById($plateformId);
        if (empty($plateformModel)) {
            return false;
        }
        return $plateformModel->isEffective() && $plateformModel->isWebPlateform();
    }


}