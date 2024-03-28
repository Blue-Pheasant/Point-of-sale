<script type="text/javascript">
document.title = 'Chi đơn hàng';
</script>
<?php

use app\core\Application;
?>
<div class="row">
    <div class="col-lg-6">
        <section class="panel">
            <header class="panel-heading">
                <h1>Thông tin chi tiết đơn hàng</h1>
                <?php
        $path = Application::$app->request->getPath();
        if (strpos($path, 'reject')) {
          echo '<a href="/admin/orders/rejected">Trở về</a>';
        } else echo '<a href="/admin/orders/accepted">Trở về</a>';
        ?>
            </header>
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <dt>Mã đơn hàng</dt>
                    <dd><?= $params['orders']->getId() ?></dd>
                    <dt>Mã khách hàng</dt>
                    <dd><?= $params['orders']->getUserId() ?></dd>
                    <dt>Thanh toán</dt>
                    <dd><?= $params['orders']->getPaymentMethod() ?></dd>
                    <dt>Trạng thái</dt>
                    <dd><?= $params['orders']->getStatus() ?></dd>
                    <dt>Người nhận</dt>
                    <dd><?= $params['orders']->getDeliveryName() ?></dd>
                    <dt>Địa chỉ giao hàng</dt>
                    <dd><?= $params['orders']->getDeliveryAddress() ?></dd>
                    <dt>Số điện thoại</dt>
                    <dd><?= $params['orders']->getDeliveryPhone() ?></dd>
                    <dt>Ngày đặt hàng</dt>
                    <dd><?= $params['orders']->getDateTime() ?></dd>
                </dl>
            </div>
        </section>
    </div>
</div>