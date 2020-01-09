<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/10
 * Time: 8:57
 */
namespace app\store\dto;

class SaleRequestDto
{
    /**
     * @#name 操作类型
     * @var int
     * @require
     */
    private $saleType = 0;


    /**
     * @#name 商品id
     * @var array
     * @require
     */
    private $goodsId = [];

    /**
     * @return int
     */
    public function getSaleType(): int
    {
        return $this->saleType;
    }

    /**
     * @param int $saleType
     */
    public function setSaleType(int $saleType): void
    {
        $this->saleType = $saleType;
    }

    /**
     * @return array
     */
    public function getGoodsId(): array
    {
        return $this->goodsId;
    }

    /**
     * @param array $goodsId
     */
    public function setGoodsId(array $goodsId): void
    {
        $this->goodsId = $goodsId;
    }

    /**
     * 设置上下架条件
     * @return array|bool
     */
    public function setSale()
    {
        if(empty($this->saleType))
            return false;
        $condition = [];
        if($this->saleType === 1) //上架
        {
            $condition['is_up'] = 1;
        } else //下架
        {
            $condition['is_up'] = 0;
        }
        return $condition;
    }


}