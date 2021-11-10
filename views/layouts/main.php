<?php

$path = str_replace("\\", "/", "http://" . $_SERVER['SERVER_NAME'] . __DIR__ . "/");
$path = str_replace($_SERVER['DOCUMENT_ROOT'], "", $path);

?>

<!doctype html:5>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-
        DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns"
        crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cookie&family=Dancing+Script&family=Raleway:ital@1&family=Zen+Antique+Soft&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../../public/css/home.css">
    <link rel="stylesheet" href="../../public/css/pos.css">
    <link rel="stylesheet" href="../../public/css/register.css">
    <link rel="stylesheet" href="../../public/css/login.css">

    <title>Buy me</title>
</head>

<body>
    <img src="../public/images/bg_image.jpg" alt="" class="bg_body">
    <div class="wrap">
        <div id="header">
            <div class="bg_header"></div>
            <div class="container">
                <div class="row div_1">
                    <div class="col-xl-3 col-lg-3 col-md-12">
                        <img src="../public/images/dish.png" alt="" style="float: left;">
                        <div class="thuongHieu">Buy Me</div>
                    </div>
                    <div class="col-xl-9 col-lg-9 inform">
                        <div>
                            <img src="../public/images/address.png" alt="">
                            123A MyStreet <br>
                            MyCity, VietNam
                        </div>
                        <div>
                            <img src="../public/images/open.png" alt="">
                            Mon-Fri: 8h30 - 20h30 <br>
                            Sat-Sun: 8h00 - 17h30
                        </div>
                        <div></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="nav">
            <ul class="navbar-nav nav-ul nav navbar-pill">
                <li class="nav-item">
                    <a class="nav-link" href="#header">Home</a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="#About_us">About us</a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item dropdown">
                    <a class="nav-link" href="#" onclick="show_products();">Products</a>
                </li>
                <div class="dropdown-divider hr" id="hr_products"></div>
                <li id="sub_products" class="menu_small_screen">
                    <ul>
                        <li><a href="#products">Món 1</a></li>
                        <hr>
                        <li><a href="#products">Món 2</a></li>
                        <hr>
                        <li><a href="#products">Món 3</a></li>
                        <hr>
                        <li><a href="#products">Nước 1</a></li>
                        <hr>
                        <li><a href="#products">Nước 2</a></li>
                        <hr>
                        <li><a href="#products">Nước 3</a></li>
                        <hr>
                        <li><a href="#products">Món 4</a></li>
                        <hr>
                        <li><a href="#products">Món 5</a></li>
                        <hr>
                        <li><a href="#products">Món 6</a></li>
                        <hr>
                        <li><a href="pos.html">POS</a></li>
                        <hr>
                    </ul>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="show_services();">Services</a>
                </li>
                <div class="dropdown-divider hr" id="hr_services"></div>
                <li id="sub_services" class="menu_small_screen">
                    <ul>
                        <li><a href="#services">Order Online Anytime</a></li>
                        <hr>
                        <li><a href="#services">Quick-Saving Delivery</a></li>
                        <hr>
                        <li><a href="#services">Comfortable Payment</a></li>
                        <hr>
                        <li><a href="#services">Listen To Your Feeling</a></li>
                        <hr>
                    </ul>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="#our_company">Our Company</a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="#contact">Contact</a>
                </li>
                <div class="dropdown-divider"></div>
            </ul>
                <?php

                use app\core\Application;

                if (Application::isGuest() == 1) : ?>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="login__button nav-link" href="/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="/register">Đăng ký</a>
                    </li>
                </ul>
                <?php else : ?>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/logout">
                            Chào <?php echo Application::$app->user->getDisplayName() ?> (Đăng xuất)
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="/cart">
                            Giỏ hàng
                        </a>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </nav>
    </div>

    <div class="main">
        <div class="container">
            <?php if (app\core\Application::$app->session->getFlash('success')) : ?>
            <div class="alert alert-success">
                <p><?php echo app\core\Application::$app->session->getFlash('success') ?></p>
            </div>
            <?php endif; ?>
            {{content}}
        </div>
    </div>
    <div id="contact">
            <div class="footer">
                &copy; Copyright 2021 <span>BUY ME</span>
                <3 </div>
            </div>
        </div>
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="../public/js/home.js"></script>   
</body>

</html>