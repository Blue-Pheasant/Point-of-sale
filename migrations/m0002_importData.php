<?php

use app\core\Application;

class m0002_importData
{
    public function up()
    {

        $db = Application::$app->db;
        $sql = "INSERT INTO thepos.stores (id,address,status,image_url,created_at,updated_at,open_time,phone) VALUES
            ('1','Hà Nội','hoạt động','image/hanoi.png',current_timestamp(),current_timestamp(),'7:00 - 22:00',''),
            ('2','Đà Nẵng','hoạt động','image/danang.png',current_timestamp(),current_timestamp(),'7:00 - 22:00',''),
            ('3','Thành Phố Hồ Chí Minh','hoạt động','image/hochiminh.png',current_timestamp(),current_timestamp(),'7:00 - 22:00','');";
        $db->pdo->exec($sql);


        $db = Application::$app->db;
        $sql = "INSERT INTO thepos.category (id,name,created_at,updated_at) VALUES
            ('1','Đồ chay',current_timestamp(),current_timestamp()),
            ('2','Đồ nướng',current_timestamp(),current_timestamp()),
            ('3','Món bình dân',current_timestamp(),current_timestamp()),
            ('4','Thức ăn vặt',current_timestamp(),current_timestamp()),
            ('5','Đồ uống',current_timestamp(),current_timestamp());";
        $db->pdo->exec($sql);

        $db = Application::$app->db;
        $sql = "INSERT INTO thepos.products (id,category_id,name,image_url,price,description,created_at,updated_at) VALUES
            ('1','1','Cơm chay','image/dish_1.jpg',30000,'Phục vụ khách hàng có nhu cầu.',current_timestamp(),current_timestamp()),
            ('2','2','Gà nướng muối ớt','image/dish_2.jpg',80000,'Gà nướng thơm ngon, độ cay vừa phải, kích thích vị giác.',current_timestamp(),current_timestamp()),
            ('3','3','Cơm tấm','image/dish_3.jpg',20000,'Ăn kèm với nước chấm đặc biệt.',current_timestamp(),current_timestamp()),
            ('4','2','Mực nướng','image/dish_1.jpg',50000,'Mực tươi sống, được nhập ngay trong ngày',current_timestamp(),current_timestamp()),
            ('5','2','Sườn heo nướng','image/dish_2.jpg',50000,'Được ướp tinh tế, hương vị đậm đà.',current_timestamp(),current_timestamp()),
            ('6','2','Thịt xiên nướng','image/dish_3.jpg',50000,'Còn gì ngon hơn món ăn tuổi thơ của bạn.',current_timestamp(),current_timestamp()),
            ('7','5','Coca Cola','image/drink_1.jpg',10000,'Sản phẩm của Coca Cola',current_timestamp(),current_timestamp()),
            ('8','5','Pesi','drink_2.jpg',10000,'Sản phẩm của Pesico',current_timestamp(),current_timestamp()),
            ('9','5','Chanh muối','drink_3.jpg',20000,'Bổ sung năng lượng cho ngày dài mệt mỏi.',current_timestamp(),current_timestamp()),
            ('10','5','Bia Sài Gòn','drink_1.jpg',15000,'Còn gì hơn khi có bạn bè.',current_timestamp(),current_timestamp());";
        $db->pdo->exec($sql);
    }
}