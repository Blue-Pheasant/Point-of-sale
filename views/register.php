<div class="card-regester">
    <div class="card-header">
        <h1>Đăng ký</h1>
    </div>
    <div class="card-body">
        <?php $form = app\core\Form\Form::begin('', "post") ?>
            <div class="row">
                <div class="col">
                    <?php echo $form->field($model, 'firstname') ?>
                </div>
                <div class="col">
                    <?php echo $form->field($model, 'lastname') ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php echo $form->field($model, 'email') ?>
                </div>    
                <div class="col">
                    <?php echo $form->field($model, 'phone_number') ?>
                </div>
            </div> 
            <div class="row">
                <div class="col">
                    <?php echo $form->field($model, 'address') ?>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php echo $form->field($model, 'password')->passwordField() ?>
                </div>   
                <div class="col">
                    <?php echo $form->field($model, 'passwordConfirm')->passwordField() ?>
                </div>  
            </div>
            <div class="remember-me">
                <input type="checkbox" required>
                <span style="color: #000000">Tôi chấp nhận các điều khoản của dịch vụ</span>
            </div>
            <div class="btn">
                <button type="submit" class="button">Đăng ký</button>
            </div>
        <?php app\core\form\Form::end() ?>
    </div>
    <div class="card-footer">
        <div class="login">
            Bạn đã có tài khoản chưa ?
            <a href="/login"><button id="login-link">Đăng nhập</button></a>
        </div>
    </div>
</div>
