<?php

function orderStatus($status)
{
    $statusStr = '';
    switch ($status) {
        case 'processing':
            $statusStr = 'Đang xử lý';
            break;
        case 'done':
            $statusStr = 'Đã hoàn thành';
            break;
        case 'cancel':
            $statusStr = 'Đã hủy';
            break;
        default:
            break;
    }
    return $statusStr;
}

?>
<form action="", method="post">
    <button type="submit" class="password-button"><h6>Làm trống</h6></button>
</form>
<div class="order-page">
    <div class="menu__header">
        <img class="menu-image" src="/images/orders.png" alt="menu-image" />
        <h3>Đơn hàng của bạn</h3>
    </div>
    <div class="order-page__list">
        <div class="container">
            <div class="order-page__header">
                <div class="row">
                    <div class="col">
                        Số thứ tự
                    </div>
                    <div class="col">
                        Mã đơn hàng
                    </div>
                    <div class="col">
                        Tình trạng đơn hàng
                    </div>
                    <div class="col">
                        Ngày đặt hàng
                    </div>
                </div>
            </div>
            <?php
            $count = 0;
            foreach ($params['orders'] as $param) {
                $count += 1;
                echo '<div class="order-page__item">
                <a href="/order?id=' . $param->id . '">
                    <div class="row">
                        <div class="col">
                            ' . $count . '
                        </div>
                        <div class="col">
                            ' . $param->id . '
                        </div>
                        <div class="col">
                            ' . orderStatus($param->status) . '
                        </div>
                        <div class="col">
                            ' . $param->created_at . '
                        </div>
                    </div>
                </a>
            </div>';
            }
            ?>

        </div>
    </div>
</div>