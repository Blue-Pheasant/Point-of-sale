<div class="container rounded bg-white mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="text-left">Profile Settings</h4>
    </div><br>
    <a href="/admin">Trở về</a>
    <?php $form = app\core\Form\Form::begin('', "post") ?><br>
    <div class="row">
        <div class="col-md-5 border-right">
            <div class="p-3 py-5">
                <div class="row mt-2">
                    <div class="col-md-6">
                        <?php echo $form->field($user, 'firstname') ?>
                    </div>
                    <div class="col-md-6">
                        <?php echo $form->field($user, 'lastname') ?>   
                    </div>
                </div><br>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <?php echo $form->field($user, 'phone_number') ?><br>
                    </div>
                    <div class="col-md-12">
                        <?php echo $form->field($user, 'address') ?><br>
                    </div>
                    <div class="col-md-12">
                        <?php echo $form->field($user, 'email') ?><br>
                    </div>
                    <div class="col-md-12">
                        <?php echo $form->field($user, 'role') ?><br>
                    </div>
                </div><br><br>
                <div class="mt-5 text-center">
                    <button class="btn btn-primary profile-button" type="submit">Cập nhật</button>
                </div>
            </div>
        </div>
    </div>
    <?php app\core\form\Form::end() ?>
</div>
