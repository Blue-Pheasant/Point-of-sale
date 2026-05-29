<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Smoke test to confirm the PHPUnit toolchain runs.
 *
 * Replace / extend with real unit tests as the test suite grows (roadmap T17).
 */
final class SmokeTest extends TestCase
{
    public function testTrueIsTrue(): void
    {
        $this->assertTrue(true);
    }
}
