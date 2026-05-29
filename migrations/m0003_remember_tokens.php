<?php

use app\Core\Application;

class m0003_remember_tokens
{
    public function up(): void
    {
        $db  = Application::$app->db;
        $sql = '
            CREATE TABLE IF NOT EXISTS `remember_tokens` (
                `id`         VARCHAR(100)  COLLATE utf8mb4_vietnamese_ci NOT NULL,
                `user_id`    VARCHAR(100)  COLLATE utf8mb4_vietnamese_ci NOT NULL,
                `token_hash` VARCHAR(255)  COLLATE utf8mb4_vietnamese_ci NOT NULL,
                `expires_at` TIMESTAMP     NOT NULL,
                `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_token_hash` (`token_hash`),
                KEY `idx_user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;
        ';
        $db->pdo->exec($sql);
    }

    public function down(): void
    {
        Application::$app->db->pdo->exec('DROP TABLE IF EXISTS `remember_tokens`');
    }
}
