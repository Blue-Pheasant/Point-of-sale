<?php

namespace app\Common;

/**
 * Class Pagination
 *
 * This class is responsible for handling the pagination of data in the application.
 * It provides a method to calculate pagination details such as the current page, total pages,
 * whether there are previous or next pages, and the offset for data fetching.
 *
 * @package app\Common
 */
class Pagination
{
    /**
     * Method paginate
     *
     * Calculates the pagination details based on the limit, current page number, and total count.
     *
     * @param int $limit The limit of items per page.
     * @param int $pageNum The current page number.
     * @param int $total The total count of items.
     * @return array Returns an array containing the pagination details.
     */
    public static function paginate(int $limit, int $pageNum = 1, int $total): array
    {
        $totalPage = ceil($total / $limit);
        $currentPage = min($pageNum, $totalPage);
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