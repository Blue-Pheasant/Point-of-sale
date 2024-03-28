<script type="text/javascript">
  document.title = 'Tạo sản phẩm';
</script> 
<div class="row">
  <div class="col-lg-12">
    <section class="panel">
      <header class="panel-heading">
        <h1>Tạo sản phẩm</h1>
        <a href="/admin/products">Trở về</a>
      </header>
      <div class="panel-body">
        <?php $form = app\core\Form\Form::begin('', "post") ?>
            <div class="form-group col-md-4">
              <label for="category_id">Tên mục</label>
              <input type="text" class="form-control" id="category_id" name="category_id" placeholder="Tên mục">
            </div>
            <div class="form-group col-md-4">
              <label for="name">Tên sản phẩm</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Tên sản phẩm">
            </div>
            <div class="form-group col-md-4">
              <label for="price">Giá</label>
              <input type="text" class="form-control" id="price" name="price" placeholder="Giá">
            </div>
            <div class="form-group col-md-4">
              <label for="image_url">Địa chỉ hình ảnh</label>
              <input type="text" class="form-control" id="image_url" name="image_url" placeholder="Địa chỉ hình ảnh">
            </div>
            <div class="form-row">
              <div class="form-group col-md-12">
                <label for="description">Mô tả sản phẩm</label>
                <input type="text" class="form-control" id="description" name="description" placeholder="Mô tả sản phẩm">
              </div>
            </div>
          <div class="form-row">
            <div class="col-md-4">
              <button type="submit" class="btn btn-primary"><i class="fa fa-cart-plus"></i> Tạo sản phẩm</button>
            </div>
          </div>
        <?php app\core\form\Form::end() ?>
      </div>
    </section>
  </div>
</div>