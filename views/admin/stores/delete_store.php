<script type="text/javascript">
  document.title = 'Xoá cửa hàng';
</script> 
<div class="row">
  <div class="col-lg-6">
    <section class="panel">
      <header class="panel-heading">
        <h1>Xóa cửa hàng</h1>
        <a href="/admin/stores">Trở về</a>
      </header>
      <div class="panel-body">
        <?php $form = app\core\Form\Form::begin('', "post") ?>
          <input type="hidden" name="id" id="id" value="<?= $params['model']->getId() ?>" />
          <dl class="dl-horizontal">
            <dt>Mã cửa hàng</dt><dd><?= $params['model']->getId() ?></dd>
            <dt>Trạng thái</dt><dd><?= $params['model']->getStatus() ?></dd>
            <dt>Địa chỉ</dt><dd><?= $params['model']->getAddress() ?></dd>
            <dt>Giờ mở cửa</dt><dd><?= $params['model']->getOpentime() ?></dd>
            <dt>Số điện thoại</dt><dd><?= $params['model']->getHotline() ?></dd>
          </dl>
          <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Xóa cửa hàng</button>
        <?php app\core\form\Form::end() ?>
      </div>
    </section>
  </div>
</div>