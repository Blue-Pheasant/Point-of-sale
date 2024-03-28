<script type="text/javascript">
  document.title = 'Danh sách các mục';
</script> 
<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading">
        <h1>Thể loại</h1>
          <a href="/admin/categories/create" class="btn btn-success">Tạo ra</a>
      </header>
      <div class="panel-body">
        <table class="table table-striped table-hover dt-datatable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th class="no-sort"></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($params['category'] as $categoryModel) { 
            ?>
              <tr>
                <td><?=$categoryModel->getId()?></td>
                <td><?=$categoryModel->getName()?></td>
                <td>
                  <a class="fa fa-eye btn btn-info btn-sm" href="/admin/categories/details?id=<?=$categoryModel->getId()?>"></a>
                  <a class="fa fa-pencil btn btn-warning btn-sm" href="/admin/categories/edit?id=<?=$categoryModel->getId()?>"></a>
                  <a class="fa fa-trash btn btn-danger btn-sm" href="/admin/categories/delete?id=<?=$categoryModel->getId()?>"></a>
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