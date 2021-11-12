<?php

require_once('../layout/header_signup.php');

?>
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

            <label for="address"><b>Số Điện Thoại</b></label>
            <input type="text" placeholder="Nhập Số Điện Thoại" name="phonenumber" required>

            <label>
                <input type="checkbox" checked="checked" name="remember" style="margin-bottom:15px"> Nhớ Đăng Nhập
            </label>

            <div class="clearfix">
                <button type="submit" class="signupbtn">Đăng ký</button>
            </div>
        </div>
    </form>
</main>
</div>
<?php

require_once('../layout/footer.php')

?>