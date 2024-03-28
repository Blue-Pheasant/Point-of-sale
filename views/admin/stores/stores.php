<script type="text/javascript">
  document.title = 'Quản lý cửa hàng';
</script> 
<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading">
        <h1>Quản lý cửa hàng</h1>
          <a href="/admin/stores/add" class="btn btn-success">Thêm cửa hàng</a>
      </header>
      <div class="panel-body">
        <table class="table table-striped table-hover dt-datatable">
          <thead>
            <tr>
              <th>Mã cửa hàng</th>
              <th>Trạng thái</th>
              <th>Địa chỉ</th>
              <th>Giờ mở cửa</th>
              <th>Số điện thoại</th>
              <th class="no-sort"></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($params['store'] as $storeModel) { 
            ?>
              <tr>
                <td><?=$storeModel->getId()?></td>
                <td><?=$storeModel->getStatus()?></td>
                <td><?=$storeModel->getAddress()?></td>
                <td><?=$storeModel->getOpentime()?></td>
                <td><?=$storeModel->getHotline()?></td>
                <td>
                    <a class="fa fa-eye btn btn-info btn-sm" href="/admin/stores/details?id=<?=$storeModel->getId()?>"></a>
                    <a class="fa fa-pencil btn btn-warning btn-sm" href="/admin/stores/edit?id=<?=$storeModel->getId()?>"></a>
                    <a class="fa fa-trash btn btn-danger btn-sm" href="/admin/stores/delete?id=<?=$storeModel->getId()?>"></a>
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