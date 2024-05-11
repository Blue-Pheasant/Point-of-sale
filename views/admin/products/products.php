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
        <table id="table-pagination" class="table table-striped table-hover dt-datatable">
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

<script>
function updatePagination(totalRecords, activePage) {
    var paginationWrapper = document.getElementsByClassName('.pagination')[0];
    paginationWrapper.innerHTML = ''; // Clear existing pagination

    var totalPages = Math.ceil(totalRecords / 10); // Assuming 10 records per page

    for (var i = 1; i <= totalPages; i++) {
      var listItem = document.createElement('li');
      listItem.classList.add('paginate_button');
      if (i === activePage) {
        listItem.classList.add('active');
      }

      var link = document.createElement('a');
      link.href = '#';
      link.setAttribute('aria-controls', 'table-pagination');
      link.setAttribute('data-dt-idx', i);
      link.setAttribute('tabindex', '0');
      link.innerText = i;

      link.addEventListener('click', function(event) {
        event.preventDefault();
        var pageNumber = parseInt(this.innerText);
        goToPage(pageNumber);
        updatePagination(totalRecords, pageNumber);
      });

      listItem.appendChild(link);
      paginationWrapper.appendChild(listItem);
    }
  }

  updatePagination(100, 1);
</script>