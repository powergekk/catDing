<?php

namespace app\index\controller;

use app\common\consts\ArrayToObjType;
use app\common\consts\HomeJumpType;
use app\common\entity\database\OrderLogDto;
use app\index\bean\UserSession;

use utils\ReflectionUtils;

class Index extends Common
{


    /**
     * 不需要验证登陆,*表示不验证登陆
     * @var array|*
     */
    protected $notCheckLoginAction = '*';


    public function index()//: IndexResp
    {
//        HomeJumpType
$da = $this->getPostInputData('goods_list', [], '', OrderLogDto::class, ArrayToObjType::AUTO);
echo var_export($this->getPostInputData('goods_list', [], '', OrderLogDto::class, ArrayToObjType::AUTO));
//        exit(json_encode(ReflectionUtils::arrayToObj(array('outContent'=>33),OrderLogDto::class, 'CATEGORY')));
        exit;
        foreach(ReflectionUtils::getClassConstants(HomeJumpType::class)  as $key => $val){
            preg_match('/#\{([^\}]*)\}/',ReflectionUtils::getClassConstantDocument(HomeJumpType::class, $key), $arr);
            echo $val."  ===>  ".$arr[1];
            echo "<br/>";
        }
//        var_export(ReflectionUtils::getClassConstant(HomeJumpType::class, "CATEGORY"));
//        preg_match('/#\{([^\}]*)\}/',ReflectionUtils::getClassDocument(HomeJumpType::class), $arr);
//        var_export($arr);
//        var_export(ReflectionUtils::getClassConstants(HomeJumpType::class));
        exit;
        return $this->indexResp->ok(UserSession::instance()->getInfo())->send();
    }


    /**
     * 4
     * #{实例化函数的后面一步}
     * 可以这一步做登陆验证
     * @return mixed
     */
    protected function _after_instance()
    {
        // TODO: Implement _after_instance() method.
    }
}
