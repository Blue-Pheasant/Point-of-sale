<?php

    require_once('../layout/header_login.php');

?>
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
                <a href="register.html"><button type="button" class="sign_up">Đăng ký tài khoản mới</button></a>
            </div>
        </form>
    </div>
</div>
<?php

    require_once('../layout/footer.php')

?>