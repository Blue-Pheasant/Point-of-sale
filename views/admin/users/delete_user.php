<script type="text/javascript">
  document.title = 'Xoá người dùng';
</script> 
<div class="row">
  <div class="col-lg-6">
    <section class="panel">
      <header class="panel-heading">
        <h1>Xóa người dùng</h1>
        <a href="/admin/users">Trở về</a>
      </header>
      <div class="panel-body">
        <?php $form = app\Core\Form\Form::begin('', "post") ?>
          <input type="hidden" name="id" id="id" value="<?= $params['userModel']->getId() ?>" />
          <dl class="dl-horizontal">
          <dt>Mã người dùng</dt><dd><?= $params['userModel']->getId() ?></dd>
          <dt>Tên người dùng</dt><dd><?= $params['userModel']->getName() ?></dd>
          <dt>Email</dt><dd><?= $params['userModel']->getEmail() ?></dd>
          <dt>Số điện thoại</dt><dd><?= $params['userModel']->getPhoneNumer() ?></dd>
          <dt>Vai trò</dt><dd><?= $params['userModel']->getRole() ?></dd>
          <dt>Địa chỉ</dt><dd><?= $params['userModel']->getAddress() ?></dd>
          </dl>
          <button type="submit" class="btn btn-danger"><i class="fa fa-trash"></i> Xóa </button>
        <?php app\Core\form\Form::end() ?>
      </div>
    </section>
  </div>
</div>