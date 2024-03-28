<script type="text/javascript">
  document.title = 'Sửa đổi mục';
</script> 
<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading">
        <h1>Sửa đổi sản phẩm</h1>
        <a href="/admin/categories">Trở về</a>
      </header>
      <div class="panel-body">
        <?php $form = app\core\Form\Form::begin('', "post") ?>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <?php echo $form->field($categoryModel, 'name') ?>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-pencil"></i> Chỉnh sửa mục</button>
                </div>
            </div>
          </div>
        <?php app\core\form\Form::end() ?>
      </div>
    </section>
  </div>
</div>