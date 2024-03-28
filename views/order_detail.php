<?php

function extraPrice($size, $price)
{
    $extraPrice = $price;
    switch ($size) {
        case 'Small':
            $extraPrice += 0;
            break;
        case 'Medium':
            $extraPrice += 3000;
            break;
        case 'Large':
            $extraPrice += 6000;
            break;
        default:
            break;
    }
    return $extraPrice;
}

function sizeContent($size)
{
    $str = '';
    switch ($size) {
        case 'Small':
            $str = 'Small';
            break;
        case 'Medium':
            $str = 'Meidum (+3.000đ)';
            break;
        case 'Large':
            $str = 'Large (+6.000đ)';
            break;
        default:
            break;
    }
    return $str;
}

function total($params)
{
    $total = 0;
    foreach ($params as $param) {
        $total += extraPrice($param->size, $param->price) * $param->quantity;
    }
    return $total;
}

?>

<div class="cart-page">
    <div class="cart-page__header">
        <h3>Đơn hàng của bạn</h3>
    </div>
    <div class="cart-page__body">
        <div class="container">
            <div class="row gx-5">
                <div class="col-md-12 col-lg-8">
                    <div class="cart-page__content">
                        <div class="cart-page__content__header">
                            <div>Đơn hàng : </div>
                        </div>
                        <div class="cart-page-divider"></div>

                        <div class="cart-page__content__body">
                            <?php
                            foreach ($params['items'] as $param) {
                                echo '<div class="cart-page-item">
                                    <div class="container">
                                        <div class="row gy-2">
                                            <div class="col-lg-2 col-md-2 col-sm-3 col-3">
                                                <img class="order-page__item-image"
                                                    src="' . $param->image_url . '" />
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-9 col-9">
                                                <h6>' . $param->name . '</h6>
                                                <div>Giá đơn vị: ' . number_format($param->price, 0, ',', '.') . ' đ</div>
                                                <div>Size: ' . sizeContent($param->size) . '</div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                                                <div>Số lượng: ' . $param->quantity . '</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            
                                            <div class="col-lg-6 col-sm-8">
                                                <div class="input-group mb-3">
                                                    <input type="text" id="cart-page__note" class="form-control"
                                                        placeholder="Ghi chú cho sản phẩm này" aria-label="note"
                                                        aria-describedby="basic-addon1" value="' . $param->note . '">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                            }
                            ?>
                        </div>


                        <div class="cart-page__content__header">
                            <div>Tổng cộng</div>
                        </div>
                        <div class="cart-page-divider"></div>
                        <div class="cart-page__content__total">
                            <div>Tạm tính</div>
                            <div><?php echo number_format(total($params['items']), 0, ',', '.') ?>đ</div>
                        </div>

                        <div class="cart-page__content__footer">
                            <div>
                                <div>Thành tiền</div>
                                <div class="cart-page-total">
                                    <?php echo number_format(total($params['items']), 0, ',', '.') ?>đ</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-4">
                    <div class="cart-page__info">
                        <div class="cart-page__content__header">
                            <div>Địa chỉ giao hàng</div>
                        </div>
                        <div class="cart-page-divider"></div>
                        <div class="cart-page__content__header">
                            <?php echo $params['order']->delivery_address ?>
                        </div>

                        <div class="cart-page__content__header">
                            <div>Thông tin người nhận</div>
                        </div>
                        <div class="cart-page-divider"></div>
                        <div class="cart-page__content__header">
                            Tên người nhận:
                            <?php echo $params['order']->delivery_name ?>
                        </div>
                        <div class="cart-page__content__header">
                            Số điện thoại: <?php echo $params['order']->delivery_phone ?>
                        </div>
                        <!-- <div class="cart-page__content__header">
                                <input type="text" class="form-control" id="delivery-note"
                                    placeholder="Ghi chú cho đơn hàng này">
                            </div> -->
                        <div class="cart-page__content__header">
                            <div>Phương thức thanh toán</div>
                        </div>
                        <div class="cart-page-divider"></div>

                        <?php
                        switch ($params['order']->payment_method) {
                            case 'cash':
                                echo
                                '<div class="order-page__content__header__checkbox">
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        <img class="image-payment" src="/images/payment/cash.jpeg">
                                        Thanh toán khi nhận hàng (tiền mặt)
                                    </label>
                                </div>';
                                break;
                            case 'momo-pay':
                                echo
                                '<div class="order-page__content__header__checkbox">
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        <img class="image-payment" src="/images/payment/momo.png">
                                        Momo
                                    </label>
                                </div>';
                                break;
                            case 'zalo-pay':
                                echo '
                                <div class="order-page__content__header__checkbox">
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        <img class="image-payment" src="/images/payment/zalo.png">
                                        ZaloPay
                                    </label>
                                </div>';
                                break;
                            case 'shopee-pay':
                                echo '
                                <div class="order-page__content__header__checkbox">
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        <img class="image-payment" src="/images/payment/shopee.png">
                                        ShopeePay
                                    </label>
                                </div>';
                                break;
                            case 'credit':
                                echo '
                                <div class="order-page__content__header__checkbox">
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        <img class="image-payment" src="/images/payment/card.png">
                                        Thẻ ngân hàng
                                    </label>
                                </div>';
                                break;
                            default:
                                break;
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/product_detail.js"></script>