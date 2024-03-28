<script type="text/javascript">
  document.title = 'Chi tiết đặt hàng';
</script> 
<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading">
        <h1>Chi tiết đặt hàng</h1>
        <a href="/admin/orders">Trở về</a>
      </header>
      <div class="panel-body">
        <table class="table table-striped table-hover dt-datatable">
          <thead>
            <tr>
              <th>Hình ảnh sản phẩm</th>
              <th>Tên sản phẩm</th>
              <th>Giá đơn vị</th>
              <th>Kích thước</th>
              <th>Số lượng</th>
              <th>Ghi chú đơn hàng</th>
              <th class="no-sort"></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($params['model'] as $orderModel) { 
            ?>
              <tr>
                <td>
                    <?php
                         echo '<img width="60" height="60"src="' . $orderModel->image_url . '">';
                    ?>
                </td>
                <td><?=$orderModel->name?></td>
                <td><?=number_format($orderModel->price, 0, ',', '.') . 'đ'?></td>
                <td><?=$orderModel->size?></td>
                <td><?=$orderModel->quantity?></td>
                <td><?=$orderModel->note?></td>   
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