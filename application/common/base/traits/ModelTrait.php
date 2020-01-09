<?php
namespace app\common\base\traits;





trait ModelTrait {
    
    /**
     * 获取主键值
     * @return Mixed_
     */
    public function getPkVal(){
        $pk = $this->getPk();
        return isset($this->$pk) ? $this->$pk : null;
    }
    
    
    
}