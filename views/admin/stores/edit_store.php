<script type="text/javascript">
  document.title = 'Sửa đổi cửa hàng';
</script> 
<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading">
        <h1>Chỉnh sửa thông tin cửa hàng</h1>
        <a href="/admin/stores">Trở về</a>
      </header>
      <div class="panel-body">
        <?php $form = app\core\Form\Form::begin('', "post") ?>
            <div class="form-group col-md-6">
                <?php echo $form->field($storeModel, 'status') ?>
            </div>
            <div class="form-group col-md-6">
                <?php echo $form->field($storeModel, 'address') ?>
            </div>
            <div class="form-group col-md-6">
                <?php echo $form->field($storeModel, 'phone') ?>
            </div>
            <div class="form-group col-md-6">
                <?php echo $form->field($storeModel, 'open_time') ?>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <?php echo $form->field($storeModel, 'image_url') ?>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fa fa-cart-plus"></i>Lưu</button>
                </div>
            </div>
        <?php app\core\form\Form::end() ?>
      </div>
    </section>
  </div>
</div>