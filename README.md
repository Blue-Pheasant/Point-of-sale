#PHP MVC SYSTEM
Using php to create a coffee sale coffee website <br /> <br />
With mvc structure we divide project into many folder: <br />

./bootstrap: contain bootstrap framework. <br />
./font: contain font. <br />
./img: contain img. <br />
./views: contain files to display front-end. <br />
./models: contain php files to deploy back-end. <br />
./controllers: contain php to deploy back-end. <br />
./migrations: update database. <br />
./middlewares: contain middleware. <br />
./core: contain the core classes. <br />

It's a simple php-mvc template version 1.0. We will maintain and develop in future.

## SET UP

### Tạo mysql database:

Đầu tiên, sử dụng DBMS của bạn tạo một connection mysql database với cấu hình sau:

Server host : localhost
Database : thecoffeehouse
Port : 3306
Username : root
Password : (có hay không cũng được)

### Cài đặt Composer (package manager của PHP)

Bước 1: Vào https://getcomposer.org/ để tải Composer về rồi install tùy theo máy.

Bước 2: Kiểm tra xem Composer đã đựoc cài chưa (phải cài được mới run được project)

```bash
composer --version
```

Note: Nếu đã có folder vender trên máy rồi thì hãy xóa nó đi rồi sang bước 3.

Bước 3: Chạy install package theo composer.json :

```bash
composer install
```

Cách dùng composer giống với xài npm với NodeJS, đều là package manager thôi, có gì tham khảo.

### Tạo dotenv

Vào folder project, tạo một file .env rồi bỏ vào config sau:

```bash
DB_DSN=mysql:host=localhost;dbname=pos
DB_USER=root
DB_PASSWORD=
```

trong đó, password của bạn là gì thì điền vô, khôn

### Chay migration:

Mở terminal lên rồi lệnh sau:

```bash
php migrations.php
```

Terminal trả về như sau là bạn đã chạy migrate được rồi:

```bash
[2021-10-28 19:10:49] - Applying migration m0001_initial.php
[2021-10-28 19:10:49] - Applyied migration m0001_initial.php
```

Nếu không được như vậy thì hãy drop hết table trong database rồi chạy migrate lại.

## Run project

Để chạy project, chạy lệnh sau:

```bash
cd public
php -S localhost:8000
```
