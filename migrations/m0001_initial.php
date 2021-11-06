<?php

use app\core\Application;

class m0001_initial
{
    public function up()
    {
        $db = Application::$app->db;
        $sql = "
            CREATE TABLE `admins` (
            `id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `firstname` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `lastname` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `username` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `password` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `position` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

            -- --------------------------------------------------------

            --
            -- Table structure for table `cart`
            --

            CREATE TABLE `cart` (
            `id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `customer_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `status` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

            -- --------------------------------------------------------

            --
            -- Table structure for table `cart_detail`
            --

            CREATE TABLE `cart_detail` (
            `product_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `cart_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `quantity` int(11) NOT NULL,
            `note` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

            -- --------------------------------------------------------

            --
            -- Table structure for table `category`
            --

            CREATE TABLE `category` (
            `id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `name` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

            -- --------------------------------------------------------

            --
            -- Table structure for table `customers`
            --

            CREATE TABLE `customers` (
            `id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `firstname` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `lastname` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `email` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `phone_number` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `password` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `image_url` varchar(4000) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `address` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `ward_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `district_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `province_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

            -- --------------------------------------------------------

            --
            -- Table structure for table `feedbacks`
            --

            CREATE TABLE `feedbacks` (
            `id` int(11) NOT NULL,
            `customer_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `product_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `stars` int(11) NOT NULL,
            `comment` int(11) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

            -- --------------------------------------------------------

            --
            -- Table structure for table `orders`
            --

            CREATE TABLE `orders` (
            `id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `customer_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `payment_method` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `status` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

            -- --------------------------------------------------------

            --
            -- Table structure for table `order_detail`
            --

            CREATE TABLE `order_detail` (
            `product_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `order_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `quantity` int(11) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

            -- --------------------------------------------------------

            --
            -- Table structure for table `products`
            --

            CREATE TABLE `products` (
            `id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `category_id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `name` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `image_url` varchar(1000) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `price` int(12) NOT NULL,
            `description` varchar(4000) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

            -- --------------------------------------------------------

            --
            -- Table structure for table `stores`
            --

            CREATE TABLE `stores` (
            `id` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `address` varchar(1000) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `status` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `image_url` varchar(4000) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `open_time` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `phone` varchar(100) COLLATE utf8mb4_vietnamese_ci NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vietnamese_ci;

            --
            -- Indexes for dumped tables
            --

            --
            -- Indexes for table `admins`
            --
            ALTER TABLE `admins`
            ADD PRIMARY KEY (`id`);

            --
            -- Indexes for table `cart`
            --
            ALTER TABLE `cart`
            ADD PRIMARY KEY (`id`),
            ADD KEY `cart_customer_fk` (`customer_id`);

            --
            -- Indexes for table `cart_detail`
            --
            ALTER TABLE `cart_detail`
            ADD KEY `cart_fk` (`cart_id`),
            ADD KEY `product_fk` (`product_id`);

            --
            -- Indexes for table `category`
            --
            ALTER TABLE `category`
            ADD PRIMARY KEY (`id`);

            --
            -- Indexes for table `customers`
            --
            ALTER TABLE `customers`
            ADD PRIMARY KEY (`id`);

            --
            -- Indexes for table `feedbacks`
            --
            ALTER TABLE `feedbacks`
            ADD PRIMARY KEY (`id`),
            ADD KEY `feedback_customer_fk` (`customer_id`),
            ADD KEY `feedback_product_fk` (`product_id`);

            --
            -- Indexes for table `orders`
            --
            ALTER TABLE `orders`
            ADD PRIMARY KEY (`id`),
            ADD KEY `order_customer_fk` (`customer_id`);

            --
            -- Indexes for table `order_detail`
            --
            ALTER TABLE `order_detail`
            ADD KEY `order_product_fk` (`product_id`),
            ADD KEY `order_fk` (`order_id`);

            --
            -- Indexes for table `products`
            --
            ALTER TABLE `products`
            ADD PRIMARY KEY (`id`),
            ADD KEY `category_id` (`category_id`);

            --
            -- Indexes for table `stores`
            --
            ALTER TABLE `stores`
            ADD PRIMARY KEY (`id`);

            --
            -- Constraints for dumped tables
            --

            --
            -- Constraints for table `cart`
            --
            ALTER TABLE `cart`
            ADD CONSTRAINT `cart_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

            --
            -- Constraints for table `cart_detail`
            --
            ALTER TABLE `cart_detail`
            ADD CONSTRAINT `cart_fk` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`),
            ADD CONSTRAINT `product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

            --
            -- Constraints for table `feedbacks`
            --
            ALTER TABLE `feedbacks`
            ADD CONSTRAINT `feedback_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
            ADD CONSTRAINT `feedback_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

            --
            -- Constraints for table `orders`
            --
            ALTER TABLE `orders`
            ADD CONSTRAINT `order_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

            --
            -- Constraints for table `order_detail`
            --
            ALTER TABLE `order_detail`
            ADD CONSTRAINT `order_fk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
            ADD CONSTRAINT `order_product_fk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

            --
            -- Constraints for table `products`
            --
            ALTER TABLE `products`
            ADD CONSTRAINT `category_id` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`);
        ";
        $db->pdo->exec($sql);
    }

    public function down()
    {
        $db = Application::$app->db;
        $sql = "DROP TABLE users;";
        $db->pdo->exec($sql);
    }
}