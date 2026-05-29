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
    public static function paginate(int $limit, int $pageNum, int $total): array
    {
        if ($pageNum == null) {
            $pageNum = 1;
        }

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

    /**
     * Runs the common "count → paginate → fetch page → hydrate" flow shared by
     * the service `getAll*`/search methods, so the pattern lives in one place.
     *
     * The given builder already carries every WHERE/filter for the listing; its
     * row count is reused for pagination, then LIMIT/OFFSET are applied and the
     * page rows are mapped through $hydrate.
     *
     * @template T
     * @param QueryBuilder $query The pre-filtered builder (no LIMIT/OFFSET yet).
     * @param int $limit The page size.
     * @param int $page The 1-based page number.
     * @param callable(array<string, mixed>): T $hydrate Maps a row to a model.
     * @return array{list: array<int, T>, pagination: array<string, mixed>}
     */
    public static function paginateResults(QueryBuilder $query, int $limit, int $page, callable $hydrate): array
    {
        $total = $query->count();
        $pagination = self::paginate($limit, $page, $total);

        $rows = $query
            ->limit($limit)
            ->offset((int) $pagination['offset'])
            ->get();

        return [
            'list' => array_map($hydrate, $rows),
            'pagination' => $pagination,
        ];
    }
}
