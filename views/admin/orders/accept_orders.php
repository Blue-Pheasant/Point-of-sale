<script type="text/javascript">
document.title = 'Đơn hàng đã duyệt';
</script>
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">
                <h1>Đơn hàng đã duyệt</h1>
                <a href="/admin/orders">Trở về</a>
            </header>
            <div class="panel-body">
                <table class="table table-striped table-hover dt-datatable">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Mã khách hàng</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Người nhận</th>
                            <th>Địa chỉ giao hàng</th>
                            <th>Số điện thoại</th>
                            <th>Ngày đặt hàng</th>
                            <th class="no-sort">Tuỳ chọn</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
            foreach ($params['orders'] as $orderModel) {
            ?>
                        <tr>
                            <td><?= $orderModel->getId() ?></td>
                            <td><?= $orderModel->getUserId() ?></td>
                            <td><?= $orderModel->getPaymentMethod() ?></td>
                            <td><?= $orderModel->getStatus() ?></td>
                            <td><?= $orderModel->getDeliveryName() ?></td>
                            <td><?= $orderModel->getDeliveryAddress() ?></td>
                            <td><?= $orderModel->getDeliveryPhone() ?></td>
                            <td><?= $orderModel->getDateTime() ?></td>
                            <td>
                                <a class="fa fa-eye btn btn-info btn-sm"
                                    href="/admin/orders/accepted/details?id=<?= $orderModel->getId() ?>"></a>
                                <a class="fa fa-trash btn btn-danger btn-sm"
                                    href="/admin/orders/accepted/delete?id=<?= $orderModel->getId() ?>"></a>
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