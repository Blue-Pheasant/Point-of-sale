<?php

declare(strict_types=1);

namespace Tests\Unit;

use app\Common\Pagination;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see Pagination::paginate()} — offset / total pages /
 * prev-next flags (roadmap T17).
 */
final class PaginationTest extends TestCase
{
    public function testFirstPageOffsetIsZero(): void
    {
        $result = Pagination::paginate(10, 1, 95);

        $this->assertSame(0, $result['offset']);
        $this->assertSame(1, $result['currentPageNum']);
    }

    public function testTotalPagesRoundsUp(): void
    {
        // 95 items, 10 per page → 10 pages (last page partially full).
        $this->assertSame(10.0, Pagination::paginate(10, 1, 95)['lastPageNum']);
    }

    public function testTotalPagesExactDivision(): void
    {
        $this->assertSame(10.0, Pagination::paginate(10, 1, 100)['lastPageNum']);
    }

    public function testOffsetForMiddlePage(): void
    {
        // page 3 of size 10 → skip the first 20 rows.
        $this->assertSame(20, Pagination::paginate(10, 3, 95)['offset']);
    }

    public function testHasPrevIsFalseOnFirstPage(): void
    {
        $result = Pagination::paginate(10, 1, 95);

        $this->assertFalse($result['hasPrev']);
        $this->assertTrue($result['hasNext']);
    }

    public function testHasNextIsFalseOnLastPage(): void
    {
        $result = Pagination::paginate(10, 10, 95);

        $this->assertTrue($result['hasPrev']);
        $this->assertFalse($result['hasNext']);
    }

    public function testMiddlePageHasBothNeighbours(): void
    {
        $result = Pagination::paginate(10, 5, 95);

        $this->assertTrue($result['hasPrev']);
        $this->assertTrue($result['hasNext']);
    }

    public function testRequestingPageBeyondLastClampsToLast(): void
    {
        // Asking for page 99 of a 10-page set lands on the last page.
        $result = Pagination::paginate(10, 99, 95);

        // ceil() yields a float, so currentPageNum clamps to 10.0.
        $this->assertEquals(10, $result['currentPageNum']);
        $this->assertFalse($result['hasNext']);
    }

    public function testTotalCountIsEchoedBack(): void
    {
        $this->assertSame(95, Pagination::paginate(10, 1, 95)['totalCount']);
    }
}
