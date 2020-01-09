<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/10
 * Time: 9:46
 */
namespace app\store\dto;

class PlatformRequestDto
{
    /**
     * @#name 商品名称
     * @var string
     */
    private $goodsName = '';

    /**
     * @#name 商品sku
     * @var string
     */
    private $sku = '';

    /**
     * @#name 商品规格
     * @var string
     */
    private $goodsSize = '';

    /**
     * @#name 是否标记
     * @var string
     */
    private $isMarked = '';

    /**
     * @#name 页码
     * @var int
     */
    private $page = 1;

    /**
     * @#name 条数
     * @var int
     */
    private $pageSize = 10;

    /**
     * @return string
     */
    public function getGoodsName(): string
    {
        return $this->goodsName;
    }

    /**
     * @param string $goodsName
     */
    public function setGoodsName(string $goodsName): void
    {
        $this->goodsName = $goodsName;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    /**
     * @return string
     */
    public function getGoodsSize(): string
    {
        return $this->goodsSize;
    }

    /**
     * @param string $goodsSize
     */
    public function setGoodsSize(string $goodsSize): void
    {
        $this->goodsSize = $goodsSize;
    }

    /**
     * @return string
     */
    public function getIsMarked(): string
    {
        return $this->isMarked;
    }

    /**
     * @param string $isMarked
     */
    public function setIsMarked(string $isMarked): void
    {
        $this->isMarked = $isMarked;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     */
    public function setPageSize(int $pageSize): void
    {
        $this->pageSize = $pageSize;
    }


}