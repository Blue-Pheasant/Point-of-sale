<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <link rel="stylesheet" href="../public/css/home.css">
    <title>Document</title>
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
        </div>
        <div id="menuBtn" onclick="nav()"><img src="../public/images/menu.png" id="menu"></div>
        <div id="banner">
            <div class="container" id="navigation">
                <div class="row div_2">
                    <div class="nav">
                        <nav class="navbar navbar-expand-lg">
                            <ul class="navbar-nav">
                                <li class="nav-item active">
                                    <a class="nav-link" href="#header">Home</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link" href="#About_us">About Us</a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="http://example.com"
                                        id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        Products
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                        <li><a class="dropdown-item" href="#products">Món 1</a></li>
                                        <li><a class="dropdown-item" href="#products">Món 2</a></li>
                                        <li><a class="dropdown-item" href="#products">Món 3</a></li>
                                        <li class="dropdown-submenu dropright">
                                            <a class="dropdown-item dropdown-toggle" data-toggle="dropdown"
                                                href="#">Drinks</a>
                                            <ul class="dropdown-menu" style="border: unset; padding-top: 0;">
                                                <a class="dropdown-item" href="#products">Nước 1</a>
                                                <a class="dropdown-item" href="#products">Nước 2</a>
                                                <a class="dropdown-item" href="#products">Nước 3</a>
                                            </ul>
                                        </li>
                                        <li><a class="dropdown-item" href="#products">Món 4</a></li>
                                        <li><a class="dropdown-item" href="#products">Món 5</a></li>
                                        <li><a class="dropdown-item" href="#products">Món 6</a></li>
                                        <li><a class="dropdown-item" href="pos.html">POS</a></li>
                                    </ul>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                        Services
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#services">Order Online Anytime</a>
                                        <a class="dropdown-item" href="#services">Quick-Saving Delivery</a>
                                        <a class="dropdown-item" href="#services">Comfortable Payment</a>
                                        <a class="dropdown-item" href="#services">Listen To Your Feeling</a>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#our_company">
                                        Our Company
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#contact">Contact</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
                    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
                </ol>
                <div class="carousel-inner">
                    <div class="carousel-item active img" style="position: relative;">
                        <img class="d-block w-100" src="../public/images/banner.jpg" alt="First slide">
                        <div class="script" style="position: absolute; top: 0;">
                            <h1>WELCOME TO</h1>
                            <h4>Our Restaurant</h4>
                            <p>--------------------------</p>
                            <h3>Best Food, Good Food</h3>
                            <a href=""><button>Book a table now</button></a>
                        </div>
                    </div>
                    <div class="carousel-item img">
                        <img class="d-block w-100" src="../public/images/banner_5.PNG" alt="First slide">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </div>
        <div class="container" id="About_us">
            <div class="row">
                <div class="col-xl-12 col-md-12">
                    <img src="../public/images/chef_2.jpg" alt="" style="width: 100%; height: auto;">
                    <div class="script" style="position: absolute; top: 0; font-family: 'Dancing Script', cursive;">
                        <h1>Over <span>250 BRANCHS</span><br> Around The World</h1>
                        <h4>We will satisfy even the most demanding guests</h4>
                        <p>--------------------------</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="products">
            <div class="on"></div>
            <div class="under"></div>
            <div class="bg_product"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-md-12 title">With 120 different dishes</div>
                    <div class="col-xl-4 col-md-4 col-sm-4 col-12 img">
                        <img src="../public/images/dish_1.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="script">
                            <h3>Món 1</h3>
                            <hr>
                            <p>Tên món</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-4 col-12 img">
                        <img src="../public/images/dish_3.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="script">
                            <h3>Món 2</h3>
                            <hr>
                            <p>Tên món</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-4 col-12 img">
                        <img src="../public/images/dish_4.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="script">
                            <h3>Món 3</h3>
                            <hr>
                            <p>Tên món</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4 col-md-4 col-sm-4 col-12 img">
                        <img src="../public/images/dish_1.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="script">
                            <h3>Món 4</h3>
                            <hr>
                            <p>Tên món</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-4 col-12 img">
                        <img src="../public/images/dish_3.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="script">
                            <h3>Món 5</h3>
                            <hr>
                            <p>Tên món</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-4 col-12 img">
                        <img src="../public/images/dish_4.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="script">
                            <h3>Món 6</h3>
                            <hr>
                            <p>Tên món</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12 col-md-12 title">And more than 20 kinds of soft drinks</div>
                    <div class="col-xl-4 col-md-4 col-sm-4 col-12 img">
                        <img src="../public/images/drink_1.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="script">
                            <h3>Nước 1</h3>
                            <hr>
                            <p>Tên món</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-4 col-12 img">
                        <img src="../public/images/drink_2.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="script">
                            <h3>Nước 2</h3>
                            <hr>
                            <p>Tên món</p>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-4 col-12 img">
                        <img src="../public/images/drink_3.jpg" alt="">
                        <div class="overlay"></div>
                        <div class="script">
                            <h3>Nước 3</h3>
                            <hr>
                            <p>Tên món</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="services">
            <div class="container">
                <div class="bg_services"></div>
                <div class="row">
                    <div class="col-xl-12 col-md-12 title">
                        <div>Our Services</div>
                    </div>
                    <div class="col-xl-3 col-md-3 col-sm-6 col-12">
                        <div class="bor_der">
                            <img src="../public/images/order_online.png" alt="">
                            <div class="service">Order Online Anytime</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 col-sm-6 col-12">
                        <div class="bor_der">
                            <img src="../public/images/delivery.png" alt="">
                            <div class="service">Quick-Saving Delivery</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 col-sm-6 col-12">
                        <div class="bor_der">
                            <img src="../public/images/payment.png" alt="">
                            <div class="service">Comfortable Payment</div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 col-sm-6 col-12">
                        <div class="bor_der">
                            <img src="../public/images/customer_care.png" alt="">
                            <div class="service">Listen To Your Feeling</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="our_company">
            <div class="bg_our_company"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-md-12 title">Our Company</div>
                    <div class="col-xl-6">
                        <div class="slider">
                            <a href="#slide-1-1">1</a>
                            <a href="#slide-1-2">2</a>
                            <a href="#slide-1-3">3</a>
                            <a href="#slide-1-4">4</a>
                            <div class="slides">
                                <div id="slide-1-1">Tổng giám đốc</div>
                                <div id="slide-1-2">Giám đốc</div>
                                <div id="slide-1-3">Giám đốc nhân sự</div>
                                <div id="slide-1-4">Giám đốc hành chính</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="slider">
                            <a href="#slide-2-1">1</a>
                            <a href="#slide-2-2">2</a>
                            <a href="#slide-2-3">3</a>
                            <a href="#slide-2-4">4</a>
                            <a href="#slide-2-5">5</a>
                            <a href="#slide-2-6">6</a>
                            <div class="slides">
                                <div id="slide-2-1">Trưởng phòng 1</div>
                                <div id="slide-2-2">Trưởng phòng 2</div>
                                <div id="slide-2-3">Phó phòng 1</div>
                                <div id="slide-2-4">Phó phòng 2</div>
                                <div id="slide-2-5">Nhân viên 1</div>
                                <div id="slide-2-6">Nhân viên 2</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="contact">
            <div class="footer">
                &copy; Copyright 2021 <span>BUY ME</span>
                <3 </div>
            </div>
        </div>
        <script src="../public/js/home.js"></script>
</body>

</html>