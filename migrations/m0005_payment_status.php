<?php

use app\Core\Application;

/**
 * Adds payment-gateway tracking columns to `orders` (roadmap T22).
 *
 * `payment_status` records where the order sits in the payment lifecycle
 * (pending → paid / failed); `transaction_id` stores the reference returned by
 * the payment gateway so a callback can be reconciled to its order. Both are
 * nullable so existing rows (placed before payment integration) stay valid.
 */
class m0005_payment_status
{
    public function up(): void
    {
        $db  = Application::$app->db;
        $sql = "
            ALTER TABLE `orders`
                ADD COLUMN `payment_status` VARCHAR(20) COLLATE utf8mb4_vietnamese_ci NOT NULL DEFAULT 'pending',
                ADD COLUMN `transaction_id` VARCHAR(100) COLLATE utf8mb4_vietnamese_ci DEFAULT NULL,
                ADD KEY `idx_transaction_id` (`transaction_id`);
        ";
        $db->pdo->exec($sql);
    }

    public function down(): void
    {
        $db = Application::$app->db;
        $db->pdo->exec(
            'ALTER TABLE `orders`
                DROP KEY `idx_transaction_id`,
                DROP COLUMN `payment_status`,
                DROP COLUMN `transaction_id`;'
        );
    }
}
