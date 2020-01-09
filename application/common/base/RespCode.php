<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/25
 * Time: 17:27
 */
namespace app\common\base;

/**
 * response code
 * Class ResponseCode
 * 
 * @package app\index\base
 */
final class RespCode
{

    /**
     * #{成功状态}
     * @var string
     */
    const OK = "SU00001";


    /**
     * #{运行异常}
     * @var string
     */
    const RUN_ERR = "RU00001";



    /**
     * #{请求URL异常}
     * @var string
     */
    const RUN_URL = "RU00002";

    /**
     * #{登录验证异常}
     * @var string
     */
    const ERR_TOKEN = "ER00000";

    /**
     * #{错误状态码}
     * @var string
     */
    const ERR = "ER00001";

    /**
     * #{签名异常}
     * @var string
     */
    const ERR_SIGN = "ER00002";

    /**
     * #{请求方法异常(post, get)}
     * @var string
     */
    const ERR_METHOD = "ER00003";

    /**
     * #{请求参数异常}
     * @var string
     */
    const ERR_PARAM = "ER00004";

    /**
     * #{平台异常}
     * @var string
     */
    const ERR_PLATEFORM_ID = "ER00005";




    
    // 可加业务状态码，
}