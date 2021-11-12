<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-
        DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cookie&family=Dancing+Script&family=Raleway:ital@1&family=Zen+Antique+Soft&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/index.css">
    <link rel="stylesheet" href="../../public/css/sign_up.css">
    <title>Document</title>
</head>

<body>
    <div class="wrap">
        <div id="header">
            <div class="bg_header"></div>
            <div class="container">
                <div class="row div_1">
                    <div class="col-xl-3 col-lg-3 col-md-12">
                        <img src="../../public/image/dish.png" alt="" style="float: left;">
                        <div class="thuongHieu">Buy Me</div>
                    </div>
                    <div class="col-xl-9 col-lg-9 inform">
                        <div>
                            <a href="../about_us/sign_up.php">Đăng ký</a> /
                            <a href="../about_us/login.php">Đăng nhập</a>
                        </div>
                        <div>
                            <img src="../../public/image/address.png" alt="">
                            123A MyStreet <br>
                            MyCity, VietNam
                        </div>
                        <div>
                            <img src="../../public/image/open.png" alt="">
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
                    <a class="nav-link" href="../about_us/sign_up.php">Đăng ký</a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="../about_us/login.php">Đăng nhập</a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="../about_us/index.php">Home</a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="#About_us">About us</a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="show_services();">Services</a>
                </li>
                <div class="dropdown-divider hr" id="hr_services"></div>
                <li id="sub_services" class="menu_small_screen">
                    <ul>
                        <li><a href="../about_us/services.php">Order Online Anytime</a></li>
                        <hr>
                        <li><a href="../about_us/services.php">Quick-Saving Delivery</a></li>
                        <hr>
                        <li><a href="../about_us/services.php">Comfortable Payment</a></li>
                        <hr>
                        <li><a href="../about_us/services.php">Listen To Your Feeling</a></li>
                        <hr>
                    </ul>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="../about_us/our_company.php">Our Company</a>
                </li>
                <div class="dropdown-divider"></div>
                <li class="nav-item">
                    <a class="nav-link" href="../about_us/pos.php">POS</a>
                </li>
                <div class="dropdown-divider"></div>
            </ul>
        </div>
        <div id="menuBtn" onclick="nav()"><img src="../../public/image/menu.png" id="menu"></div>
        <div id="banner">
            <div class="container" id="navigation">
                <div class="row div_2">
                    <div class="nav">
                        <nav class="navbar navbar-expand-lg">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="../about_us/index.php">Home</a>
                                </li>
                                <li class="nav-item dropdown about_us">
                                    <a class="nav-link" href="#About_us">About Us</a>
                                </li>
                                <li class="nav-item dropdown services">
                                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                                        Services
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="../about_us/services.php">Order Online Anytime</a>
                                        <a class="dropdown-item" href="../about_us/services.php">Quick-Saving Delivery</a>
                                        <a class="dropdown-item" href="../about_us/services.php">Comfortable Payment</a>
                                        <a class="dropdown-item" href="../about_us/services.php">Listen To Your Feeling</a>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="our_company.php">
                                        Our Company
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="pos.php">
                                        POS
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>