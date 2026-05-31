<?php

declare(strict_types=1);

namespace Tests\Unit;

use app\Services\PricingService;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for {@see PricingService} (roadmap T14) — the single source of
 * truth for per-size pricing.
 */
final class PricingServiceTest extends TestCase
{
    public function testSmallHasNoSurcharge(): void
    {
        $this->assertSame(0, PricingService::surcharge(PricingService::SIZE_SMALL));
    }

    public function testMediumSurcharge(): void
    {
        $this->assertSame(3000, PricingService::surcharge(PricingService::SIZE_MEDIUM));
    }

    public function testLargeSurcharge(): void
    {
        $this->assertSame(6000, PricingService::surcharge(PricingService::SIZE_LARGE));
    }

    public function testUnknownSizeHasNoSurcharge(): void
    {
        $this->assertSame(0, PricingService::surcharge('Gigantic'));
    }

    public function testUnitPriceAddsSurchargeToBase(): void
    {
        $this->assertSame(25000.0, PricingService::unitPrice(22000, PricingService::SIZE_MEDIUM));
    }

    public function testUnitPriceForSmallEqualsBase(): void
    {
        $this->assertSame(22000.0, PricingService::unitPrice(22000, PricingService::SIZE_SMALL));
    }

    public function testLineTotalMultipliesByQuantity(): void
    {
        // (22000 + 6000 large) * 3 = 84000
        $this->assertSame(84000.0, PricingService::lineTotal(22000, PricingService::SIZE_LARGE, 3));
    }

    public function testLineTotalIsZeroForZeroQuantity(): void
    {
        $this->assertSame(0.0, PricingService::lineTotal(22000, PricingService::SIZE_MEDIUM, 0));
    }
}
