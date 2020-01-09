<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/29
 * Time: 8:33
 */

namespace utils;


class PageUtils
{

    /**
     * 格式化页码数
     * @param $page
     * @param int $maxPage
     * @return int
     */
    static public function formatPage($page, int $maxPage = 9999999)
    {
        $nowPage = intval($page);
        if ($nowPage < 1) {
            return 1;
        } elseif ($nowPage > $maxPage) {
            return $maxPage;
        } else {
            return $page;
        }
    }


    /**
     * 格式化页面记录数
     * @param $pageSize
     * @return int
     */
    static public function formatPageSize($pageSize)
    {
        $nowPageSize = intval($pageSize);
        if ($nowPageSize < 5) {
            return 10;
        } else {
            return $nowPageSize;
        }
    }


    /**
     * 格式化页码查询返回
     * @param int $page
     * @param int $pageSize
     * @param int $total
     * @param array $list
     * @return array
     */
    static public function formatPageResult(int $page, int $pageSize, int $total, array $list)
    {
        return [
            'page' => $page,
            'pageSize' => $pageSize,
            'total' => $total,
            'totalPage' => ceil($total/$pageSize),
            'list' => $list
        ];
    }


    /**
     * 判断是否有当前页内容
     * @param int $page
     * @param int $pageSize
     * @param int $total
     * @return bool
     */
    static public function hasItems(int $page, int $pageSize, int $total)
    {
        return $total > ($page - 1) * $pageSize;
    }

}