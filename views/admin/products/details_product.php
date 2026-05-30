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
          <dt>Tồn kho</dt>
          <dd>
            <?= e((string) $params['productModel']->getStockQuantity()) ?>
            <?php if ($params['productModel']->isOutOfStock()) { ?>
              <span class="badge badge-danger label label-danger">Hết hàng</span>
            <?php } elseif ($params['productModel']->isLowStock()) { ?>
              <span class="badge badge-warning label label-warning">Sắp hết</span>
            <?php } ?>
          </dd>
          <dt>Ngưỡng sắp hết</dt><dd><?= e((string) $params['productModel']->getLowStockThreshold()) ?></dd>
        </dl>

        <hr>
        <h4>Điều chỉnh tồn kho</h4>
        <form action="/admin/products/adjust-stock?id=<?= e($params['productModel']->getId()) ?>" method="post" class="form-inline">
          <input type="hidden" name="_csrf" value="<?= e(\app\Middlewares\CsrfMiddleware::token()) ?>">
          <div class="form-group">
            <label for="delta">Thay đổi số lượng (+ nhập kho / − xuất kho)</label>
            <input type="number" class="form-control" id="delta" name="delta" value="0">
          </div>
          <button type="submit" class="btn btn-primary"><i class="fa fa-cubes"></i> Cập nhật tồn kho</button>
        </form>
      </div>
    </section>
  </div>
</div>