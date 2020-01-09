<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/10
 * Time: 18:37
 */

namespace utils;

use app\common\exceptions\ParamsRuntimeException;


/**
 * 反射工具类
 * Class ReflectionUtils
 * @package utils
 */
class ReflectionUtils
{

    /**
     * @#name const的变量
     * @var string
     */
    const DEF_KEY = 'key';


    /**
     * @#name const的变量值
     * @var string
     */
    const DEF_VALUE = 'val';

    /**
     * @#name const的注释名
     * @var string
     */
    const DOCUMENT_NAME = 'name';

//=========== 数组转 对象时，是否必传值 =========
    /**
     * @#name 必填字段
     * @var string
     */
    const DEF_REQUIRE = 'require';


    /**
     * @#name 不必填字段
     * @var string
     */
    const DEF_NOT_REQUIRE = 'notRequire';

//------^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^


//=========== 数组转 对象时，是否必传值 =========
    /**
     * @#name 允许转成JSON
     * @var string
     */
    const DEF_JSON_SHOW = 'toJson';


    /**
     * @#name 不允许转JSON
     * @var string
     */
    const DEF_JSON_NOT_SHOW = 'notJson';

//------^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^


    /**
     * @#name 实例化对象
     * @var array
     */
    static private $reflectionInstance = [
        'ReflectionClass' => [],
    ];

    /**
     * @#name 对应类的常量数组
     * @var array
     */
    static private $classConstantList = [];

    /**
     * @#name 获取类的所有常量
     * @param string $className
     * @return array
     * @throws \ReflectionException
     */
    static public function getClassConstants(string $className)
    {
        return self::getReflectionClassInstance($className)->getConstants();
    }


    /**
     * @#name 获取类的常量
     * @param string $className
     * @param string $key
     * @return mixed
     * @throws \ReflectionException
     */
    static public function getClassConstant(string $className, string $key)
    {
        return self::getReflectionClassInstance($className)->getConstant($key);
    }


    /**
     * @#name 获取类的常量注释
     * @param string $className
     * @param string $text
     * @param null $func
     * @return bool|mixed|string
     * @throws \ReflectionException
     */
    static public function getClassConstantDocument(string $className, string $text, $func = null)
    {
        $reflectionConstantDocument = self::getReflectionClassInstance($className)->getReflectionConstant($text);
        if (empty($reflectionConstantDocument)) {
            throw new \RuntimeException("未获取到常量属性");
        }
        $doc = $reflectionConstantDocument->getDocComment();
        if (is_null($func)) {
            return $doc;
        } elseif (is_string($func) || $func instanceof \Closure) {
            return call_user_func_array($func, [$doc]);
        } else {
            throw new \RuntimeException("方法:[$func]异常");
        }
    }


    /**
     * @#name 获取备注
     * @param string $className
     * @return bool|string
     * @throws \ReflectionException
     */
    static public function getClassDocument(string $className)
    {
        return self::getReflectionClassInstance($className)->getDocComment();
    }


    /**
     * @#name 获取反射类实例
     * @param string $className
     * @return \ReflectionClass
     * @throws \ReflectionException
     */
    static private function getReflectionClassInstance(string $className)
    {
        if (!isset(self::$reflectionInstance['ReflectionClass'][$className]) || !self::$reflectionInstance['ReflectionClass'][$className] instanceof \ReflectionClass) {
            self::$reflectionInstance['ReflectionClass'][$className] = new \ReflectionClass($className);
        }
        return self::$reflectionInstance['ReflectionClass'][$className];
    }


    /**
     * @#name 获取类的常量数组
     * @param string $className
     * @return array
     * @throws \ReflectionException
     */
    static public function getClassConstantList(string $className)
    {
        if (!isset(self::$classConstantList[$className])) {
            $data = self::getClassConstants($className);
            if (empty($data)) {
                return [];
            }

            $list = [];
            foreach ($data as $key => $val) {
                $da = self::getClassConstantDocument($className, $key, function ($text) use ($key, $val) {
                    if (preg_match_all('/@#([^\s\n]*)\s+([^\r\n]+)/', $text, $arr)) {
                        $d = [self::DEF_KEY => $key, self::DEF_VALUE => $val];
                        for ($i = 0; $i < count($arr[0]); ++$i) {
                            $keyName = $arr[1][$i];
                            if (isset($d[$keyName])) {
                                user_error($keyName . " 重复定义!");
                            }
                            $aValue = $arr[2][$i];
                            $d[$keyName] = $aValue;
                        }
                        return $d;
                    }
                    return [];
                });

                if (!empty($da)) {
                    $list[] = $da;
                }
            }
            self::$classConstantList[$className] = $list;
        }
        return self::$classConstantList[$className];
    }


    /**
     * @#name 验证const的变量值是否存在
     * @param string $className
     * @param string $val
     * @return bool
     * @throws \ReflectionException
     */
    static public function hasConstsVal(string $className, string $val)
    {
        $list = self::getClassConstantList($className);
        if (empty($list)) {
            return false;
        } else {
            $valList = array_column($list, self::DEF_VALUE);
            return in_array($val, $valList);
        }
    }


    /**
     * @#name 通过const的val获取变量值名
     * @param string $className
     * @param string $val
     * @param string $valueName
     * @return null
     * @throws \ReflectionException
     */
    static public function getInfoByConstsVal(string $className, string $val, string $valueName = self::DOCUMENT_NAME)
    {
        $list = self::getClassConstantList($className);
        if (empty($list)) {
            return null;
        } else {
            foreach ($list as $l) {
                if ($l[self::DEF_VALUE] == $val) {
                    return $l[$valueName];
                }
            }
            return null;
        }
    }

    /**
     * @#name 获取类属性注释
     * @param string $className
     * @param string $text
     * @return mixed
     * @throws \ReflectionException
     */
    static public function getClassPropertyDocument(string $className, string $text, $func = null)
    {
        $reflectionProperty = self::getReflectionClassInstance($className)->getProperty($text);
        if (empty($reflectionProperty)) {
            throw new \RuntimeException("未获取到属性");
        }
        $doc = $reflectionProperty->getDocComment();
        if (is_null($func)) {
            return $doc;
        } elseif (is_string($func) || $func instanceof \Closure) {
            return call_user_func_array($func, [$doc]);
        } else {
            throw new \RuntimeException("方法:[$func]异常");
        }
    }


    /**
     * 数组映射到对象
     * @param array $data
     * @param string $className
     * @param bool $must
     * @return object
     * @throws \ReflectionException
     */
    static public function arrayToObj(array $data, string $className = '', bool $must = false)
    {
        $reflectionObj = self::getReflectionClassInstance($className);
        //实例
        $classInstance = $reflectionObj->newInstance();
        $methods = $reflectionObj->getMethods(\ReflectionProperty::IS_PUBLIC);
        //类上是否标注必需填充
        $mustRequire = !empty(self::docMustRequire($reflectionObj->getDocComment()));

        foreach ($methods as $method) {
            //验证是否为内容字段
            if (preg_match('/^set([A-Z_][0-9a-zA-Z_]*)$/', $method->getName(), $arr)) {
                //处理，优先使用小驼峰，然后是下划线
                $property = lcfirst(StringUtils::toCamelCase($arr[1]));
                if (self::getReflectionClassInstance($className)->hasProperty($property)) {
                    $propertyDocument = self::getClassPropertyDocument($className, $property);
                } elseif (self::getReflectionClassInstance($className)->hasProperty(StringUtils::toUnderScore($property))) {
                    $propertyDocument = self::getClassPropertyDocument($className, StringUtils::toUnderScore($property));
                } else {
                    continue;
                }


                $propertyMustRequire = self::docMustRequire($propertyDocument);
                //是否必填
                $fieldMustRequire = $propertyMustRequire === true || ($mustRequire === true && is_null($propertyMustRequire));

                $dataValue = null;
                if (isset($data[$property])) {
                    $dataValue = $data[$property];
                } elseif (isset($data[StringUtils::toUnderScore($property)])) {
                    $dataValue = $data[StringUtils::toUnderScore($property)];
                } elseif ($fieldMustRequire) {
                    throw new ParamsRuntimeException("属性:[$property] 或 [" . StringUtils::toUnderScore($property) . "]不能为空");
                }

                //如果有值，则填充
                if (!is_null($dataValue)) {
                    //默认只传入一个值
                    $parameter = $method->getParameters()[0];
                    //验证填充内容是否为对象
                    if ($parameter->getClass()) {
                        //如果必填，但内容不为数组
                        if ($fieldMustRequire && !is_array($dataValue)) {
                            throw new ParamsRuntimeException("属性:[$property] 或 [" . StringUtils::toUnderScore($property) . "]不类型异常!");
                        }
                        //内容为数组
                        if (is_array($dataValue)) {
                            $value = self::arrayToObj($dataValue, $method->getParameters()[0]->getClass()->getName());
                        } else {
                            $value = null;
                        }
//                    } elseif($parameter->getType()->getName() === 'array'){
////                        //验证是否对象数组 TODO 后期优化
                    } else {
                        $value = $dataValue;
                    }

                    //
                    if (!is_null($value)) {
                        call_user_func([$classInstance, $method->getName()], $value);
                    }

                }


            }
        }
        return $classInstance;
    }

    /**
     * 数组list映射到对象list
     * @param array $data
     * @param string $className
     * @param bool $must
     * @return array
     * @throws \ReflectionException
     */
    static public function arrayToListObj(array $data, string $className = '', bool $must = false)
    {
        $list = [];
        if (!ArrayUtils::isAssocArray($data)) {
            throw new \RuntimeException("arrayToListObj 只支持 list");
        }
        foreach ($data as $d) {
            $list[] = self::arrayToObj($d, $className, $must);
        }
        return $list;
    }


    /**
     * 数组[list]映射到对象[list]
     * @param array $data
     * @param string $className
     * @param bool $must
     * @return array
     * @throws \ReflectionException
     */
    static public function arrayToListOrObj(array $data, string $className = '', bool $must = false)
    {
        if (ArrayUtils::isAssocArray($data)) {
            $list = [];
            foreach ($data as $d) {
                $list[] = self::arrayToObj($d, $className, $must);
            }
            return $list;
        } else {
            return self::arrayToObj($data, $className, $must);
        }

    }

    /**
     * 验证文档是否需要强制不能为空,bool：有标识，null:未标识
     * @param string $text
     * @return bool|null
     */
    static private function docMustRequire(string $text)
    {
        if (preg_match_all('/@' . self::DEF_REQUIRE . '\s+[^\r\n]+/', $text)) {
            return true;
        } else if (preg_match_all('/@' . self::DEF_NOT_REQUIRE . '\s+[^\r\n]+/', $text)) {
            return false;
        } else {
            return null;
        }
    }

}

