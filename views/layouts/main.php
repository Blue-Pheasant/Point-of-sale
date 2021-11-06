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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <link rel="stylesheet" href="/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/menu.css">
    <link rel="stylesheet" href="/css/product_detail.css">
    <link rel="stylesheet" href="/css/normalize.css">
    <link rel="stylesheet" href="/css/cart.css">

    <title>Kaffee store</title>
</head>

<body>
    <div class=" header">

        <nav class="navbar navbar-expand-lg ">
            <a class="navbar-brand" href="/">
                <img class="logo" alt="logo" src='/images/logo/logo-4.png'>
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/menu">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/collection">Tumblr collection</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/stores">Cửa hàng</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">Về KAFFEE STORE</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Liên hệ</a>
                    </li>
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
    <div class="footer">
        <div class="footer__inner">
            <h6>Copyright @ 2021 KAFFEE STORE. All rights reversed.</h6>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous">
    </script>
    <script src="/js/product_detail.js"></script>
</body>

</html>