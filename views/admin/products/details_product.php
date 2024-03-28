<script type="text/javascript">
  document.title = 'Thông tin sản phẩm';
</script> 
<div class="row">
  <div class="col-lg-6">
    <section class="panel">
      <header class="panel-heading">
        <h1>Thông tin chi tiết sản phẩm</h1>
        <a href="/admin/products">Trở về</a>
      </header>
      <div class="panel-body">
        <dl class="dl-horizontal">
          <dt>ID</dt><dd><?= $params['productModel']->getId() ?></dd>
          <dt>Category</dt><dd><?= $params['productModel']->getCategory() ?></dd>
          <dt>Description</dt><dd><?= $params['productModel']->getDescription() ?></dd>
          <dt>Price</dt><dd><?= $params['productModel']->getPrice() ?></dd>
        </dl>
      </div>
    </section>
  </div>
</div>