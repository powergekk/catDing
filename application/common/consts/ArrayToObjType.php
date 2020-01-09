<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/6
 * Time: 20:25
 */

namespace app\common\consts;


/**
 * 数组转对象的类型
 * Class ArrayToObjType
 * @package app\common\consts
 */
class ArrayToObjType
{

    /**
     * @#name list
     * @var string
     */
    const LIST = 'list';


    /**
     * @#name 对象
     * @var string
     */
    const OBJECT = 'object';


    /**
     * @#name 自适应: 对象|list
     * @var string
     */
    const AUTO = 'auto';

}