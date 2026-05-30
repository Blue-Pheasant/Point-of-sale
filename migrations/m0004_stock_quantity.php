<?php

use app\Core\Application;

/**
 * Adds inventory tracking to products (roadmap T20).
 *
 * `stock_quantity` holds the units currently on hand; `low_stock_threshold`
 * is the level at or below which the admin UI flags the product as running
 * low. Both default to 0 so existing rows remain valid.
 */
class m0004_stock_quantity
{
    public function up(): void
    {
        $db  = Application::$app->db;
        $sql = '
            ALTER TABLE `products`
                ADD COLUMN `stock_quantity` INT(11) NOT NULL DEFAULT 0,
                ADD COLUMN `low_stock_threshold` INT(11) NOT NULL DEFAULT 0;
        ';
        $db->pdo->exec($sql);
    }

    public function down(): void
    {
        $db = Application::$app->db;
        $db->pdo->exec(
            'ALTER TABLE `products`
                DROP COLUMN `stock_quantity`,
                DROP COLUMN `low_stock_threshold`;'
        );
    }
}
