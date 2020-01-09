<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 9:56
 */

namespace app\store\behavior;


use think\Response;

class ResponseSendBehavior
{

    /**
     * @param Response $response
     * @return bool
     */
    public function run(Response $response)
    {
//        $content = $response->getContent();
//        if ($content instanceof ComResp) {
//            $content->setData($content);
//            $content->setTime(date('Y-m-d H:i:s'));
//            if ($content->isNeedSign()) {
//                $content->sign();
//            }
//            $response->content($content->sendJson());
//        }
//        return true;
    }
}