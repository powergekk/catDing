<?php

$indexResp = \app\index\base\IndexResp::instance();

$params = request()->param();

//组装基础参数
if($indexResp->isInit()){
    if(isset($params['plateformId'])){
        $indexResp->setPlateformId(intval($params['plateformId']));
    }
    if(isset($params['version'])){
        $indexResp->setVersion($params['version']);
    }
}
$indexResp->exception($exception);
$response = \think\Response::create($indexResp->send(), 'json');
$response->send();
exit;
?>