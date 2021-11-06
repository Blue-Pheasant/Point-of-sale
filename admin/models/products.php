<!--
    admin/models/products.php
-->
<?php
function products_delete($id)
{
    if (isset($_GET['product_id'])) {
        $id = intval($_GET['product_id']);
    } else show_404();
    $product = get_a_record('products', $id);
    $image = 'public/upload/products/' . $product['img'];
    if (is_file($image)) {
        unlink($image);
    }
//    $image2 = 'public/upload/products/' . $product['img2'];
//    if (is_file($image2)) {
//        unlink($image2);
//    }
    global $linkconnectDB;
    $sql = "DELETE FROM products WHERE id=$id";
    mysqli_query($linkconnectDB, $sql) or die(mysqli_error($linkconnectDB));
}
function product_update()
{
    if ($_POST['product_id'] <> 0) $editDate = gmdate('Y-m-d H:i:s', time() + 7 * 3600);
    else $editDate = '0000-00-00 00:00:00';

    if ($_POST['create_at'] == NULL || $_POST['create_at'] == 'dd/mm/yyyy') $createDate = date('Y-m-d H:i:s', time() + 7 * 3600);
    else $createDate = $_POST['create_at'];

    $name = escape($_POST['name']);
    if (strlen($_POST['slug']) >= 5) $slug = slug($_POST['slug']);
    else $slug = slug($name);

    $product = array(
        'id' => intval($_POST['product_id']),
        'category_id' => intval($_POST['category_id']),
        'sub_category_id' => intval($_POST['subcategory_id']),
        'product_name' => $name,
        'slug' => $slug,
        'product_size' => escape($_POST['size']),
        'product_type' => intval($_POST['type']),
        'product_price' => intval($_POST['price']),
        'create_at' => $createDate,
        'totalView' => intval($_POST['totalview']),
        'product_description' => ($_POST['description']),
        'editDate' => $editDate
    );
    $product_id = save('products', $product);
    //upload image 1 of product
    $image_name1 = $slug . '-' . $product_id . 'img1';
    $config = array(
        'name' => $image_name1,
        'upload_path'  => 'public/upload/products/',
        'allowed_ext' => 'jpg|jpeg|png|gif',
    );
    $image = upload('img', $config);
    //Update new photos to the database
    if ($image) {
        $product = array(
            'id' => $product_id,
            'img' => $image
        );
        save('products', $product);
    }
//    //upload image 2 of product
//    $image_name2 = $slug . '-' . $product_id . 'img2';
//    $config2 = array(
//        'name' => $image_name2,
//        'upload_path'  => 'public/upload/products/',
//        'allowed_ext' => 'jpg|jpeg|png|gif',
//    );
//    $image2 = upload('img2', $config2);
//    //Update new photos to the database
//    if ($image2) {
//        $product = array(
//            'id' => $product_id,
//            'img2' => $image2
//        );
//        save('products', $product);
//    }
    //redirect if update
    header('location:admin.php?controller=product');
}
