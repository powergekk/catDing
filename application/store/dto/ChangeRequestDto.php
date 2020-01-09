<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/7
 * Time: 17:48
 */
namespace app\store\dto;

class ChangeRequestDto
{
    /**
     * @#name 调整类型
     * @var int
     * @require
     */
    private $editType = 0;


    /**
     * @#name goodsId
     * @var int
     * @require
     */
    private $goodsId = 0;


    /**
     * @#name 修改内容
     * @var string
     */
    private $contents = '';


    /**
     * @return int
     */
    public function getEditType(): int
    {
        return $this->editType;
    }

    /**
     * @param int $editType
     */
    public function setEditType(int $editType): void
    {
        $this->editType = $editType;
    }

    /**
     * @return int
     */
    public function getGoodsId(): int
    {
        return $this->goodsId;
    }

    /**
     * @param int $goodsId
     */
    public function setGoodsId(int $goodsId): void
    {
        $this->goodsId = $goodsId;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        return $this->contents;
    }

    /**
     * @param string $contents
     */
    public function setContents(string $contents): void
    {
        $this->contents = $contents;
    }

    /**
     * 返回操作类型代表的修改参数类型
     * @return array
     */
    public function setType()
    {
        $condition = [];
        if($this->editType === 1) //供价
        {
            $condition['sale_price'] = $this->contents;
        } elseif ($this->editType === 2) { //库存
            $condition['qty'] = $this->contents;
        }
        return $condition;
    }

}