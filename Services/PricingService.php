<?php

namespace app\Services;

/**
 * Single source of truth for product pricing.
 *
 * The per-size surcharges used to be hard-coded (and duplicated) in
 * {@see \app\Models\CartItem::getTotalPrice()} and
 * {@see \app\Services\OrderService::getTotalInCome()}. They now live here so a
 * price change happens in exactly one place.
 *
 * @package app\Services
 */
class PricingService
{
    public const SIZE_SMALL  = 'Small';
    public const SIZE_MEDIUM = 'Medium';
    public const SIZE_LARGE  = 'Large';

    /**
     * Surcharge (in VND) added to the base price for each size. Sizes not
     * listed here carry no surcharge.
     *
     * @var array<string, int>
     */
    private const SURCHARGES = [
        self::SIZE_MEDIUM => 3000,
        self::SIZE_LARGE  => 6000,
    ];

    /**
     * Returns the surcharge for a given size (0 when the size has none).
     */
    public static function surcharge(string $size): int
    {
        return self::SURCHARGES[$size] ?? 0;
    }

    /**
     * Returns the unit price of a product for the given size.
     */
    public static function unitPrice(float $basePrice, string $size): float
    {
        return $basePrice + self::surcharge($size);
    }

    /**
     * Returns the line total (unit price × quantity) for the given size.
     */
    public static function lineTotal(float $basePrice, string $size, int $quantity): float
    {
        return self::unitPrice($basePrice, $size) * $quantity;
    }
}
