<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="../public/css/login.css">
    <script src="../public/js/login.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300&family=Roboto:wght@100;300&display=swap" rel="stylesheet">
</head>

<body>
    <div class="tp">
        <div class="container">
            <div class="row">
                <div class="logo">
                    <a href="#"><img src="../public/images/logodemo.jpg" alt="Photo"></a>
                </div>
                <div class="listmember">
                    <ul>
                        <li class="listmem"><a href="#">Trang chủ</a></li>
                        <li class="listmem"><a href="#">About</a></li>
                        <li class="listmem"><a href="#">Menu</a></li>
                        <li class="listmem"><a href="#">Oder</a></li>
                        <li class="listmem"><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="form_login">
        <form action="action_page.php" method="post">
            <div class="conteiner">
                <h1>Đăng nhập</h1>
                <p>Xin hãy nhập biểu mẫu bên dưới để đăng nhập.</p>
                <hr>

                <label for="Username"><b>Email</b></label>
                <input type="text" placeholder="Nhập Email" name="uname" required>

                <label for="password"><b>Mật khẩu</b></label>
                <input type="password" placeholder="Nhập Mật khẩu" name="psw" required>
                
                <label>
                    <input type="checkbox" checked="checked" name="remember"> Nhớ đăng nhập
                </label>
                <button class="login" type="submit">Đăng nhập</button>
                <div class="forgotpw">
                    <a href="">Quên mật khẩu?</a>
                </div>
            </div>
            <hr>
            <div>
                <a href="#"><button type="button" class="register">Đăng ký tài khoản mới</button></a>
            </div>
        </form>
    </div>
</body>
<!--     <h1>Login</h1>
    <form method="post" action="">
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="" id="exampleInputPassword1">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form> -->
</html>