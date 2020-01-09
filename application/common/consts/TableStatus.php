<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/28
 * Time: 9:42
 */

namespace app\common\consts;


/**
 * @#name 表状态
 * Class TableStatus
 * @package app\common\consts
 */
class TableStatus
{


    /**
     * @#name 新建
     * @var string
     */
    const NEW = 'new';


    /**
     * @#name 生效
     * @var string
     */
    const EFFECTIVE = 'effective';


    /**
     * @#name 过期
     * @var string
     */
    const EXPIRE = 'expire';


    /**
     * @#name 审核中
     * @var string
     */
    const AUDITING = 'auditing';


    /**
     * @#name 审核完成
     * @var string
     */
    const AUDITED = 'audited';


    /**
     * @#name 审核拒绝
     * @var string
     */
    const REFUSED = 'refused';










}