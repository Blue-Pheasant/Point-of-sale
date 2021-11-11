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
            ('2','Đồ ăn vặt',current_timestamp(),current_timestamp()),
            ('3','Món khai vị',current_timestamp(),current_timestamp()),
            ('4','Món chính',current_timestamp(),current_timestamp()),
            ('5','Món tráng miệng',current_timestamp(),current_timestamp()),
            ('6','Đồ uống',current_timestamp(),current_timestamp());";
        $db->pdo->exec($sql);

        $db = Application::$app->db;
        $sql = "INSERT INTO thepos.products (id,category_id,name,image_url,price,description,created_at,updated_at) VALUES
            ('1','1','Hủ tiếu chay','public/images/1_1.jpg',30000,'Hủ tiếu là món ăn quen thuộc của người Việt, hủ tiếu chay cũng được nhiều người yêu thích và lựa chọn đưa vào thực đơn. Những sợi hủ tiếu dai ngon, nước súp được nêm đậm đà mà vẫn thanh đạm, rau củ làm món ăn càng hấp dẫn hơn rất nhiều.',current_timestamp(),current_timestamp()),
            ('2','1','Bún riêu chay','public/images/1_2.jpg',30000,'Bún riêu chay là một trong những món ăn rất đáng để tìm hiểu và thực hiện để cùng cả nhà thưởng thức luôn. Bởi vì chúng vô cùng ngon, nước dùng thì ngọt thanh, đậm đà hương vị của rau và nấm, đậu hũ thơm lừng và riêu cua thấm vị ngon vô cùng.',current_timestamp(),current_timestamp()),
            ('3','1','Cà ri chay','public/images/1_3.jpg',30000,'Thử ngay món cà ri chay, tuy thơm béo nhưng vẫn thanh đạm. Các loại rau củ nấu món cà ri chay thơm ngon, mềm mịn, thấm gia vị cùng nước cốt dừa beo béo kết hợp với bắp non, đậu hũ chiên và nấm mèo tạo nên một món ăn cực kỳ hấp dẫn.',current_timestamp(),current_timestamp()),
            ('4','1','Lẩu nấm chay','public/images/1_4.jpg',80000,'Cùng gia đình nhâm nhi nồi lẩu nấm chay nóng hổi vừa ngon vừa khỏe thì còn gì bằng. Lẩu chay có cách làm đơn giản nhưng rất chất lượng đấy. Nước lẩu được tạo ra từ vị ngọ thnah của rau củ và nấm, đậu hũ lúc này vừa nóng hổi vừa thơm lừng, càng ăn càng cuốn.',current_timestamp(),current_timestamp()),
            ('5','1','Mì xào chay','public/images/1_5.jpg',30000,'Mì xào chay không chỉ thơm ngon mà còn rất đẹp mắt, dùng để ăn tại nhà hay đãi tiệc đều hợp lý. Từng sợi mì dai dai, đậm đà ăn cùng với rau, củ, nấm dai ngọt hài hòa sẽ khiến bạn cứ muốn ăn mãi không ngừng.',current_timestamp(),current_timestamp()),
            ('6','1','Chả lụa chay','public/images/1_6.jpg',30000,'Ngon hết sảy với món chả lụa chay cùng cách làm đơn giản tại nhà. Chả lụa dai dai, giòn giòn, khi cắt ra từng khoang sẽ thấy được những hạt tiêu đen thơm phức, chấm cùng một ít muối tiêu là chuẩn vị.',current_timestamp(),current_timestamp()),
            ('7','1','Bò kho chay','public/images/1_7.jpg',20000,'Bò kho chay sau khi hoàn thành sẽ có mùi thơm vô cùng hấp dẫn, cho bò kho nóng hổi ra tô, thêm tí rau thơm và những loại rau khác vào, bắt đầu ăn cùng với bánh mì. Nước dùng đậm đà, thơm phức, rau củ thì chín vừa tới, món này chắc chắn sẽ làm cả nhà thích thú đấy.',current_timestamp(),current_timestamp()),
            ('8','1','Bún bò chay','public/images/1_8.jpg',40000,'Bún bò Huế thơm ngon, đậm vị với nước dùng có màu sắc đẹp mắt, vàng ươm, rau củ ngọt tự nhiên, đậu hũ mềm ngon, cay cay mùi sa tế vô cùng kích thích vị giác.',current_timestamp(),current_timestamp()),
            ('9','1','Canh nấm chay','public/images/1_9.jpg',50000,'Chỉ đơn giản là canh nấu nấm mà lại ngon hết sảy, ăn mãi không ngán. Nấm giúp cho nước dùng ngon ngọt một cách thanh đạm vô cùng, nêm nếm gia vị cho vừa phải, cho thêm tí hành ngò vào là tô canh thơm nức mũi cả nhà ngay.',current_timestamp(),current_timestamp()),
            ('10','1','Mì căn xào sả ớt','public/images/1_10.jpg',30000,'Mì căn xào sả ớt được rất nhiều người ăn chay yêu thích bởi vị ngon đậm đà, tuy đơn giản nhưng ăn vào là nghiện ngay. Mì căn vàng ươm, sả ớt thơm và cay cay, mặn mặn vừa phải, ăn với cơm nóng là chuẩn bài.',current_timestamp(),current_timestamp()),
            ('11','1','Bún thái chay','public/images/1_11.jpg',30000,'Món bún Thái chay là sự kết hợp hài hòa giữa vị chua của me, vị cay nồng của ớt và vị ngọt của thơm, món ăn này rất thích hợp ăn kèm với bún tươi và các loại rau sống như rau chuối bào, rau muống bào sợi, giá,.. Nước dùng hội tụ đủ chua - cay - mặn - ngọt chắc chắn sẽ khiến cả nhà mê mẫn.',current_timestamp(),current_timestamp()),
            ('12','1','Gỏi ngó sen chay','public/images/1_12.jpg',40000,'Ngó sen mà mang đi làm các món gỏi thì ngon còn gì bằng, ngó sen giòn giòn được trộn lẫn với nước gỏi chua ngọt cùng với dưa leo, cà rốt, nguyên liệu nào cũng tươi mát ăn vào sẽ cảm nhận được sự thanh đạm ngay.',current_timestamp(),current_timestamp()),
            ('13','2','Bánh flan vỏ trứng','public/images/2_1.jpg',15000,'Bánh flan vỏ trứng không chỉ là món ăn vặt ngon lành mà còn vô cùng đáng yêu và ngon mắt.',current_timestamp(),current_timestamp()),
            ('14','2','Bắp xào','public/images/2_2.jpg',15000,'Bắp xào với những hạt bắp nếp mềm dẻo cùng tóp mỡ, hành lá và ruốc, thơm mùi bơ béo ngậy kèm những hạt ớt bột khô ăn cay cay.',current_timestamp(),current_timestamp()),
            ('15','2','Trứng vịt lộn sốt me','public/images/2_3.jpg',15000,'Vị chua chua của me, mùi thơm thơm của hành răm cùng quả trứng vịt lộn bổ dưỡng khiến trứng vịt lộn sốt me sớm trở thành món tủ yêu thích của học trò.',current_timestamp(),current_timestamp()),
            ('16','2','Hồ lô nướng','public/images/2_4.jpg',15000,'Những viên hồ lô tròn tròn, vàng ruộm được chế biến từ thịt heo và xúc xích nướng trên than hồng khiến người đi qua phải luôn phải xuýt xoa.',current_timestamp(),current_timestamp()),
            ('17','2','Mỳ phá lấu','public/images/2_5.jpg',15000,'Đây là món ăn đặc trưng của ẩm thực vỉa hè Sài Gòn, khi kết hợp phá lấu với mỳ tôm – món ăn ruột của học sinh sinh viên sẽ nhắc bạn nhớ lại kỷ niệm tuổi học trò.',current_timestamp(),current_timestamp()),
            ('18','2','Gỏi khô bò','public/images/2_6.jpg',15000,'Một đĩa gỏi khô bò có những cọng đu đủ xanh trong, được trang trí vài lát phổi bò, đậu phộng rang vàng, rau thơm và miếng bánh phồng tôm giòn rụm từ lâu đã trở thành món ăn nổi tiếng ở Sài Gòn.',current_timestamp(),current_timestamp()),
            ('19','2','Khổ qua cà ớt','public/images/2_7.jpg',15000,'Món ăn đặc biệt có xuất xứ từ Singapore thường hay có ở quận 5. Khi thưởng thức Khổ qua cà ớt bạn sẽ cảm thấy hơi giống với món phá lấu nhưng có thêm các thành phần khác như khổ qua, cà tím và ớt dồn thịt, tuy nhiều thịt nhưng không hề ngấy, rất dễ ăn.',current_timestamp(),current_timestamp()),
            ('20','2','Hột gà trà','public/images/2_8.jpg',15000,'Món này nghe tên có vẻ tương đối lạ, và hương vị của nó cũng rất lạ. Hột gà trà thực ra chính là hột gà luộc, thả vào bát hồng trà, thay vì có màu trắng thì trứng có màu hơi nâu tiệp với màu trà.',current_timestamp(),current_timestamp()),
            ('21','2','Chuối nếp nướng than','public/images/2_9.jpg',15000,'Miếng chuối ngọt được cuộn lại bằng lớp nếp dẻo, sau đó nướng trên bếp than hồng khoảng 15 phút, đến khi miếng chuối chín vàng và phải cháy cạnh một chút thì lại càng thơm hơn.',current_timestamp(),current_timestamp()),
            ('22','2','Chè Sài Gòn','public/images/2_10.jpg',15000,'Chè Sài Gòn vừa bao gồm những nguyên liệu truyền thống như đậu, đường, nước cốt dừa béo ngậy và vị đậu phộng giòn tan thêm mứt trái cây, cốm chỉ, bột báng... thơm ngon, bắt mắt.',current_timestamp(),current_timestamp()),
            ('23','2','Cơm cháy chà bông mỡ hành','public/images/2_11.jpg',15000,'Món ăn vặt ngon lành của Sài Gòn bây giờ đó là cơm cháy chà bông nhưng có thêm mỡ hành và tương ớt.',current_timestamp(),current_timestamp()),
            ('24','2','Há cảo xíu mại','public/images/2_12.jpg',15000,'Một đĩa há cảo – xíu mại xâm xấp nước chấm, phủ lên trên một chút hành phi cùng đĩa rau răm ăn kèm.',current_timestamp(),current_timestamp()),
            ('25','3','Súp ghẹ nấm đông cô','public/images/3_1.jpg',35000,'Món súp nóng hổi, thơm ngon, sánh mịn là gợi ý hoàn hảo cho các bữa tiệc. Thịt ghẹ mềm ngọt, nấm đông cô giòn ngon rất vừa miệng lại giàu dinh dưỡng.',current_timestamp(),current_timestamp()),
            ('26','3','Súp cua','public/images/3_2.jpg',35000,'Súp cua không chỉ là món ăn sáng phổ biến mà còn xuất hiện trong thực đơn các bữa tiệc sang trọng bởi hương vị thơm ngon, bổ dưỡng, phù hợp với cả người lớn và trẻ nhỏ.',current_timestamp(),current_timestamp()),
            ('27','3','Gỏi ngó sen tôm thịt','public/images/3_3.jpg',45000,'Gỏi ngó sen tôm thịt là món ăn khai vị được yêu thích trong các bữa tiệc bởi hương vị giòn ngọt, thanh mát, thích hợp để giải nhiệt.',current_timestamp(),current_timestamp()),
            ('28','3','Chả giò hải sản','public/images/3_4.jpg',25000,'Là món ăn khai vị nhẹ nhàng, chả giò hải sản có vị béo của xốt Mayonnaise quyện với hải sản ngon ngọt, rất hợp với khẩu vị người Á Đông.',current_timestamp(),current_timestamp()),
            ('29','3','Nem nướng bánh hỏi','public/images/3_5.jpg',30000,'Món ăn tuy có nguồn gốc từ miền Trung nhưng với hương vị thơm ngon, đặc sắc đã chinh phục thực khách trên khắp mọi miền.',current_timestamp(),current_timestamp()),
            ('30','3','Súp gà nấm hương','public/images/3_6.jpg',60000,'Đây là món súp được nhiều nhà hàng lựa chọn để làm món khai vị trong các bữa tiệc. Súp gà nấm hương có cách làm đơn giản, hương vị thơm ngon và chứa hàm lượng dinh dưỡng cao.',current_timestamp(),current_timestamp()),
            ('31','3','Chả giò thịt','public/images/3_7.jpg',50000,'Chả giò thịt là món khai vị đã quá quen thuộc với thực khách khi tham dự các bữa tiệc. Tùy theo khẩu vị vùng miền mà bạn lựa chọn các nguyên liệu để làm phần nhân cho hấp dẫn.',current_timestamp(),current_timestamp()),
            ('32','3','Tôm chiên xù','public/images/3_8.jpg',50000,'Tôm chiên xù bên ngoài giòn rụm, bên trong tươi ngọt là món khoái khẩu của nhiều người. Lựa chọn món này để khai vị chắc chắn sẽ làm hài lòng thực khách.',current_timestamp(),current_timestamp()),
            ('33','4','Món bò né','public/images/4_1.jpg',50000,'Món bò né có thể kết hợp với khổ qua, đậu rồng, đậu bắp hay hoa thiên lý tùy theo sở thích của gia chủ. Thịt bò thơm mềm, rau ăn kèm giòn ngon tạo thành món ăn lạ mà quen phù hợp với mọi bữa tiệc.',current_timestamp(),current_timestamp()),
            ('34','4','Gà bó xôi','public/images/4_2.jpg',50000,'Nếu bạn đang tìm kiếm một món ăn ngon, bổ dưỡng và thích hợp cho những bữa TIỆC thì không thể bỏ qua món gà bó xôi hấp dẫn.',current_timestamp(),current_timestamp()),
            ('35','4','Lẩu cá thác lác','public/images/4_3.jpg',80000,'Lẩu cá thác lác khổ qua là món lẩu phù hợp trong các dịp sum họp gia đình hay các bữa tiệc tân gia, thôi nôi, cưới hỏi,… Tuy nhiên, bạn cần cân nhắc khi lựa chọn món này vì không phải ai cũng thưởng thức được vị đắng của khổ qua đâu nhé.',current_timestamp(),current_timestamp()),
            ('36','4','Mực hấp gừng','public/images/4_4.jpg',75000,'Mực hấp gừng giòn ngon, thơm phức, ăn hoài không ngán, vì vậy bạn không nên bỏ qua món này khi lên thực đơn đãi tiệc.',current_timestamp(),current_timestamp()),
            ('37','4','Mực xào sa tế','public/images/4_5.jpg',45000,'Mực xào sa tế không chỉ là các món ăn ngon đãi tiệc trong các bữa cơm gia đình mà còn là món đãi tiệc được ưa chuộng bởi hương vị thơm ngon, cay nồng, hấp dẫn.',current_timestamp(),current_timestamp()),
            ('38','4','Cá lóc hấp bầu','public/images/4_6.jpg',55000,'Cá lóc hấp bầu với vị đậm đà từ cá, vị ngọt thanh từ trái bầu non, là một trong những món chính thú vị trong thực đơn đãi tiệc.',current_timestamp(),current_timestamp()),
            ('39','4','Lẩu gà lá giang','public/images/4_7.jpg',75000,'Lẩu gà lá giang không còn xa lạ trong các bữa tiệc của người dân Nam Bộ. Vị chua ngọt, đậm đà của món lẩu mang lại sự ngon miệng cho thực khách.',current_timestamp(),current_timestamp()),
            ('40','4','Mực chiên xù','public/images/4_8.jpg',45000,'Bên cạnh món tôm chiên xù thì mực chiên xù cũng là món ngon đãi tiệc rất “được lòng” thực khách.',current_timestamp(),current_timestamp()),
            ('41','4','Gà hấp bia','public/images/4_9.jpg',75000,'Món gà hấp bia thơm ngon, hấp dẫn.',current_timestamp(),current_timestamp()),
            ('42','4','Tôm rang me','public/images/4_10.jpg',45000,'Với vị ngon ngọt của tôm và vị chua ngọt từ nước xốt me chắc chắn sẽ giúp bàn tiệc của bạn trở nên hấp dẫn hơn bao giờ hết.',current_timestamp(),current_timestamp()),
            ('43','4','Lẩu Thái','public/images/4_11.jpg',55000,'Lẩu Thái với vị chua ngọt đặc trưng giúp thực khách đổi vị sau khi đã thưởng thức nhiều món ăn béo ngậy.',current_timestamp(),current_timestamp()),
            ('44','4','Lẩu cá măng chua','public/images/4_12.jpg',50000,'Món lẩu này rất được yêu thích bởi hương vị đậm đà và cách nấu đơn giản, dễ thực hiện.',current_timestamp(),current_timestamp()),
            ('45','4','Tôm hấp bia','public/images/4_13.jpg',35000,'Một món ngon từ tôm.',current_timestamp(),current_timestamp()),
            ('46','5','Bánh Pía','public/images/5_1.jpg',15000,'Món bánh vô cùng thơm ngon.',current_timestamp(),current_timestamp()),
            ('47','5','Món sương sáo mật ong','public/images/5_2.jpg',25000,'Với sự kết hợp hài hòa giữa mật ong và sướng sáo làm cho món tráng miệng này vừa có khả năng làm đẹp, tốt sức khỏe lại giúp giải nhiệt cực tốt vào những ngày hè nóng nực.',current_timestamp(),current_timestamp()),
            ('48','5','Chè bưởi','public/images/5_3.jpg',20000,'Một món ăn thơm mát, ngọt bùi, giòn sật … đã làm cho bao nhiêu tín đồ của chè phải đắm đuối với nó suốt bao ngày tháng.',current_timestamp(),current_timestamp()),
            ('49','5','Bánh đậu xanh','public/images/5_4.jpg',15000,'Với một ấm trà, một chiếc bánh đậu xanh nhâm nhì sau bữa ăn thì còn gì bằng.',current_timestamp(),current_timestamp()),
            ('50','5','Cốm vòng Hà Nội','public/images/5_5.jpg',15000,'Đây là một loại đặc sản cực kỳ được nhiều người Việt thích thú.',current_timestamp(),current_timestamp()),
            ('51','5','Bánh chuối nướng','public/images/5_6.jpg',10000,'Khi ăn bánh, mời vào thì bạn sẽ cảm thấy giòn giòn, dai dai… ăn thêm lúc nữa lại thấy beo béo và cuối cùng bạn sẽ cảm nhận được miếng bánh sẽ tan ra ở trong miệng.',current_timestamp(),current_timestamp()),
            ('52','5','Bánh trôi nước','public/images/5_7.jpg',15000,'Là một trong các món tráng miệng của người Việt Nam được yêu thích nhất.',current_timestamp(),current_timestamp()),
            ('53','5','Bánh bông lan chanh dây tươi','public/images/5_8.jpg',35000,'Bạn là một tín đồ của các món tráng miệng, nhưng bạn lại không thích các món ăn quá ngọt. Vậy tại sao không thử ngay món bánh bông lan làm từ chanh dây cớ chứ ? Cam kết với bạn ăn vào là cảm giác mê ngay luôn ấy.',current_timestamp(),current_timestamp()),
            ('54','5','Dâu tây đá bào','public/images/5_9.jpg',20000,'Một món ăn mát lạnh, ngon và vô cùng bổ dưỡng. Được làm từ 2 nguồn nguyên liệu chính là dâu và đá được bào nhuyễn hòa quyện lại với nhau.',current_timestamp(),current_timestamp()),
            ('55','6','Nước chanh','public/images/drink_1.jpg',20000,'Sử dụng kèm các món hải sản.',current_timestamp(),current_timestamp()),
            ('56','6','Coca Cola','public/images/drink_2.jpg',15000,'Sản phẩm của Coca Cola.',current_timestamp(),current_timestamp()),
            ('57','6','Pesi','public/images/drink_3.jpg',15000,'Sản phẩm của Pesico.',current_timestamp(),current_timestamp()),
            ('58','6','Mirinda','public/images/drink_1.jpg',25000,'Sản phẩm của Pesico.',current_timestamp(),current_timestamp()),
            ('59','6','Nước ép','public/images/drink_2.jpg',15000,'Hỗn hợp sinh tố rau quả.',current_timestamp(),current_timestamp()),
            ('60','6','Bia Sài Gòn','public/images/drink_3.jpg',15000,'Còn gì hơn khi có bạn bè.',current_timestamp(),current_timestamp());";
        $db->pdo->exec($sql);
    }
}
