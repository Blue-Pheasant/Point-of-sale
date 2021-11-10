<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="../public/css/register.css">
</head>

<body>
    <header>
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
    </header>
    <main>
        <form action="">
            <div class="container">
                <h1>Form Đăng Ký</h1>
                <p>Xin hãy nhập biểu mẫu bên dưới để đăng ký.</p>
                <hr>

                <label for="email"><b>Email</b></label>
                <input type="text" placeholder="Nhập Email" name="email" required>
                <div class="nameuser">
                    <div class="fnameuser">
                        <label for="fn"><b>Họ</b></label>
                        <input type="text" placeholder="Nhập Họ" name="fn" required>
                    </div>
                    <div class="lnameuser">
                        <label for="ln"><b>Tên</b></label>
                        <input type="text" placeholder="Nhập Tên" name="ln" required>
                    </div>
                </div>

                <label for="psw"><b>Mật Khẩu</b></label>
                <input type="password" placeholder="Nhập Mật Khẩu" name="psw" required>

                <label for="psw-repeat"><b>Nhập Lại Mật Khẩu</b></label>
                <input type="password" placeholder="Nhập Lại Mật Khẩu" name="psw-repeat" required>

                <label for="address"><b>Địa Chỉ</b></label>
                <input type="text" placeholder="Nhập Địa Chỉ" name="address" required>

                <label>
                    <input type="checkbox" checked="checked" name="remember" style="margin-bottom:15px"> Nhớ Đăng Nhập
                </label>

                <div class="clearfix">
                    <button type="submit" class="signupbtn">Đăng ký</button>
                </div>
            </div>
        </form>
    </main>

</body>
    <h1>Create an account</h1>
    <?php $form = app\core\Form\Form::begin('', "post") ?>
    <div class="row">
        <div class="col">
            <?php echo $form->field($model, 'firstname') ?>
        </div>
        <div class="col">
            <?php echo $form->field($model, 'lastname') ?>
        </div>
    </div>
    <?php echo $form->field($model, 'email') ?>
    <?php echo $form->field($model, 'phone_number') ?>
    <?php echo $form->field($model, 'address') ?>
    <?php echo $form->field($model, 'password')->passwordField() ?>
    <?php echo $form->field($model, 'passwordConfirm')->passwordField() ?>
    <button type="submit" class="btn btn-primary">Submit</button>
    <?php app\core\form\Form::end() ?>
</html>