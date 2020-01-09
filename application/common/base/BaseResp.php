<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/18
 * Time: 16:21
 */

namespace app\common\base;


interface BaseResp
{

    /**
     * 设定状态为失败
     * @param string $msg
     * @param null $data
     * @return mixed
     */
    public function err($msg = '', $data = null);


    /**
     * 设定状态为成功
     * @param null $data
     * @return mixed
     */
    public function ok($data = null);


    /**
     * 设定状态
     * @param string $msg
     * @param string $code
     * @param null $data
     * @return mixed
     */
    public function sets($msg = '', $code = '', $data = null);



    /**
     * 异常返回
     * @param \Exception $e
     * @return $this
     */
    public function exception(\Exception $e);

}