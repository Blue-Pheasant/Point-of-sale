<script type="text/javascript">
  document.title = 'Cấp lại mật khẩu';
</script> 
<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading">
        <h1>Cấp lại mật khẩu</h1>
        <div> 
          <a href="/admin/users/edit?id=<?=$userModel->getId()?>">Trở về</a>
        </div><br>
      </header>
      <div class="panel-body">
        <?php $form = app\core\Form\Form::begin('', "post") ?>
            <div class="form-group col-md-4">
              <?php echo $form->field($userModel, 'password')->passwordField()?>
            </div>
            <div class="form-group col-md-4">
              <?php echo $form->field($userModel, 'passwordConfirm')->passwordField()?>
            </div>
            <div class="form-row">
                <div class="col-md-6">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-cart-plus"></i>Lưu</button>
                </div>
            </div>
        <?php app\core\form\Form::end() ?>
      </div>
    </section>
  </div>
</div>