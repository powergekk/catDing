<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/26
 * Time: 15:26
 */

namespace app\common\model;


use app\common\consts\ModelFormatFieldType;
use app\common\consts\YesOrNo;
use think\Model;
use utils\ArrayUtils;
use utils\ReflectionUtils;

abstract class BaseModel extends Model
{


    // 是否需要自动写入时间戳 如果设置为字符串 则表示时间字段的类型
    protected $autoWriteTimestamp = 'datetime';


    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';

    // 只读字段
    protected $readonly = ['id'];


    /**
     * 设定转换字段
     * 最外层数组的键为 返回时的数组键，对应的内容为需要处理的内容：
     * ---- key: 要填充的字段
     * ---- type: 处理方式，func:通过函数，const:通过常量类匹配,array:通过数组下标匹配
     * ---- method: 如果type=func:函数名，type=const:常量类名,type=array:数组
     * ---- value: 做为func的第二个参数，或常量的name, 或数组的key
     * @var array
     */
    protected $formatStatusFields = [
//        'del_status' => ['key'=>'del_status_name', 'method'=>YesOrNo::class, 'type'=>ModelFormatFieldType::CONST, 'value'=>'name'],
//        'del_status' => ['key'=>'del_status_name', 'method'=>['N'=>'删除'], 'type'=>ModelFormatFieldType::ARRAYS],
    ];


    /**
     * BaseModel constructor.
     * @param array $data
     */
    public final function __construct($data = [])
    {
        parent::__construct($data);

        $this->_after_instance($data);
    }


    /**
     * 实例化后调用
     */
    protected final function _after_instance($data = [])
    {
        //TODO 做相应的实例工作

    }


    /**
     * 转换当前模型对象为数组
     * @return array
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    public function toArray()
    {
        $array = parent::toArray();
        //处理反射类
        if (is_array($this->formatStatusFields) && !empty($this->formatStatusFields)) {
            foreach ($this->formatStatusFields as $field => $content) {
                //如果有当前字段
                if(isset($array[$field])){
                    //标准化
                    if(!ArrayUtils::isAssocArray($content)){
                        $content = [$content];
                    }
                    foreach($content as $formarArr){
                        $array = $this->formatAddNewField($array, $field, $formarArr);
                    }
                }
            }
        }

        return $array;
    }


    /**
     *
     * @param array $data 只处理本数组的一维
     * @param string $field
     * @param array $formatArray
     * ---- key: 要填充的字段
     * ---- type: 处理方式，func:通过函数，const:通过常量类匹配,array:通过数组下标匹配
     * ---- method: 如果type=func:函数名，type=const:常量类名,type=array:数组
     * ---- value: 做为func的第二个参数，或常量的name, 或数组的key
     * @return array
     * @throws \ReflectionException
     */
    protected final function formatAddNewField(array $data, string $field, array $formatArray)
    {
        if (isset($data[$field])) {
            $type = isset($formatArray['type']) ? $formatArray['type'] : ModelFormatFieldType::CONST;

            $key = $formatArray['key'];
            //todo 暂时只支持 ModelFormatFieldType 的方式
            switch ($type) {
                case ModelFormatFieldType::CONST:
                    //常量类
                    $class = $formatArray['method'];
                    $valueName = isset($formatArray['value']) ? $formatArray['value'] : ReflectionUtils::DOCUMENT_NAME;
                    $data[$key] = ReflectionUtils::getInfoByConstsVal($class, $data[$field], $valueName);
                    break;


                case ModelFormatFieldType::FUNC:
                    //函数
                    $func = $formatArray['method'];
                    if (isset($content['value'])) {
                        $data[$key] = call_user_func_array($func, [$data[$field], $formatArray['value']]);
                    } else {
                        $data[$key] = call_user_func_array($func, [$data[$field]]);
                    }
                    break;

                case ModelFormatFieldType::ARRAYS:
                    //数组
                    $arrayContent = $formatArray['method'];
                    if (isset($content['value'])) {
                        if (isset($arrayContent[$array[$field]][$content['value']])) {
                            $data[$key] = $arrayContent[$data[$field]][$formatArray['value']];
                        } else {
                            $data[$key] = null;
                        }
                    } else {
                        if (isset($arrayContent[$array[$field]])) {
                            $data[$key] = $arrayContent[$data[$field]];
                        } else {
                            $data[$key] = null;
                        }
                    }
                    break;
            }

        }

        return $data;
    }

    /**
     * 获取属性
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function getAttrVal($name, $default = null)
    {
        if (isset($this->$name)) {
            return $this->$name; // TODO: Change the autogenerated stub
        } else {
            return $default;
        }

    }


    /**
     * 设定MODEL的数据
     * @param array $data
     * @return bool
     */
    public function setModelData(array $data, array $fields = [])
    {
        $pk = $this->getPk();

        $tableFields = $this->getTableFields();

        foreach ($tableFields as $field) {
            if ($field != $pk && isset($data[$field])) {
                $this->setAttr($field, $data[$field]);
            }
        }
        return true;
    }

    /**
     * 设定MODEL的数据,对照fields
     * @param array $data
     * @param array $fields
     * @return bool
     */
    public function setModelArrayData(array $data, array $fields = [])
    {
        $pk = $this->getPk();

        $tableFields = $this->getTableFields();
        foreach ($fields as $field) {
            if ($field != $pk && isset($data[$field]) && in_array($field, $tableFields)) {
                $this->setAttr($field, $data[$field]);
            }
        }
        return true;
    }


    /**
     * 逻辑删除当前记录,并更新其它字段信息
     * @param array $data
     * @return false|int
     */
    public function softDel(array $data = [])
    {
        $data['del_status'] = YesOrNo::YES;
        return $this->save($data);
    }


}