<script type="text/javascript">
  document.title = 'Thêm người dùng';
</script> 
<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading">
        <h1>Thêm người dùng</h1>
        <a href="/admin/users">Trở về</a>
      </header>
      <div class="panel-body">
        <?php $form = app\core\Form\Form::begin('', "post") ?>
            <div class="form-group col-md-4">
                <?php echo $form->field($userModel, 'firstname') ?>
            </div>
            <div class="form-group col-md-4">
                <?php echo $form->field($userModel, 'lastname') ?>
            </div>
            <div class="form-group col-md-4">
                <?php echo $form->field($userModel, 'phone_number') ?>
            </div>
            <div class="form-group col-md-4">
                <?php echo $form->field($userModel, 'email') ?>
            </div>
            <div class="form-group col-md-4">
              <?php echo $form->field($userModel, 'address') ?>
            </div>
            <div class="form-group col-md-4">
              <?php echo $form->field($userModel, 'role') ?>
            </div>
            <div class="form-group col-md-6">
              <?php echo $form->field($userModel, 'password')->passwordField()?>
            </div>
            <div class="form-group col-md-6">
              <?php echo $form->field($userModel, 'passwordConfirm')->passwordField()?>
            </div>
          <div class="form-row">
            <div class="col-md-4">
              <button type="submit" class="btn btn-primary"><i class="fa fa-cart-plus"></i> Lưu </button>
            </div>
          </div>
        <?php app\core\form\Form::end() ?>
      </div>
    </section>
  </div>
</div>