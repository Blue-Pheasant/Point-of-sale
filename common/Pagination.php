<?php

namespace app\Common;

class Pagination
{
    public static function paginate($limit, $pageNum = 1, $total)
    {
        $totalPage = ceil($total / $limit);
        $currentPage = $pageNum <= $totalPage ? $pageNum : $totalPage;
        $offset = $currentPage > 0 ? ($currentPage - 1) * $limit : 0;
        $hasPrev = $currentPage > 1;
        $hasNext = $currentPage < $totalPage;
    
        return [
            'limit' => $limit,
            'offset' => $offset,
            'currentPageNum' => $currentPage,
            'totalCount' => $total,
            'hasPrev' => $hasPrev,
            'hasNext' => $hasNext,
            'prevPageNum' => $hasPrev ?? $currentPage - 1,
            'nextPageNum' => $hasNext ?? $currentPage + 1,
            'lastPageNum' => $totalPage,
        ];
    }
}