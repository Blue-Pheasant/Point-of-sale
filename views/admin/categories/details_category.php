<script type="text/javascript">
  document.title = 'Chi tiết mục';
</script> 
<div class="row">
  <div class="col-lg-6">
    <section class="panel">
      <header class="panel-heading">
        <h1>Thông tin chi tiết mục</h1>
        <a href="/admin/categories">Trở về</a>
      </header>
      <div class="panel-body">
        <dl class="dl-horizontal">
          <dt>ID</dt><dd><?= $params['model']->getId() ?></dd>
          <dt>Name</dt><dd><?= $params['model']->getName() ?></dd>
        </dl>
      </div>
    </section>
  </div>
</div>