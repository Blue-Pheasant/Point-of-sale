<script type="text/javascript">
  document.title = 'Danh sách sản phẩm';
</script> 
<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading">
        <h1>Sản phẩm</h1>
          <a href="/admin/products/create" class="btn btn-success">Tạo ra</a>
      </header>
      <div class="panel-body">
        <table class="table table-striped table-hover dt-datatable">
          <thead>
            <tr>
              <th>Mã sản phẩm</th>
              <th>Hình ảnh</th>
              <th>Mục</th>
              <th>Tên sản phẩm</th>
              <th>Giá</th>
              <th class="no-sort"></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($params['products'] as $productModel) { 
            ?>
              <tr>
                <td><?=$productModel->getId()?></td>
                <td>
                    <?php
                         echo '<img width="60" height="60"src="' . $productModel->getImageUrl() . '">';
                    ?>
                </td>
                <td><?=$productModel->getCategory()?></td>
                <td><?=$productModel->getName()?></td>
                <td><?=number_format($productModel->getPrice(), 0, ',', '.') . 'đ'?></td>
                <td>
                  <a class="fa fa-eye btn btn-info btn-sm" href="/admin/products/details?id=<?=$productModel->getId()?>"></a>
                  <a class="fa fa-pencil btn btn-warning btn-sm" href="/admin/products/edit?id=<?=$productModel->getId()?>"></a>
                  <a class="fa fa-trash btn btn-danger btn-sm" href="/admin/products/delete?id=<?=$productModel->getId()?>"></a>
                </td>
              </tr>
            <?php 
              }
            ?>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</div>