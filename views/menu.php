<?php

use app\core\Application;

?>

<div class="menu">
    <div class="menu__header">
        <img class="menu-image" src="/images/menu.png" alt="menu-image" />
        <h3>Thực đơn của chúng mình</h3>
    </div>

    <div class="menu__search">
        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="floatingInput"
                placeholder="Tìm kiếm theo tên sản phẩm bạn quan tâm">
            <label for="floatingInput">Tìm kiếm theo tên sản phẩm bạn quan tâm</label>
        </div>
    </div>

    <div class="menu__options">
        <div class="option" onclick="">
            <div class="option-image-block">
                <img src="/images/coffee-cup.png" alt="coffee-cup" class="option-image" />
            </div>
            <h6>
                Cà phê
            </h6>
        </div>
        <div class="option" onclick="">
            <div class="option-image-block">
                <img src="/images/milk-tea.png" alt="coffee-cup" class="option-image" />
            </div>
            <h6>
                Trà trái cây - Trà sửa
            </h6>
        </div>
        <div class="option" onclick="">
            <div class="option-image-block">
                <img src="/images/milkshake.png" alt="coffee-cup" class="option-image" />
            </div>
            <h6>
                Đá xay
            </h6>
        </div>
        <div class="option" onclick="">
            <div class="option-image-block">
                <img src="/images/coffee.png" alt=" coffee-cup" class="option-image" />
            </div>
            <h6>
                Thưởng thức tại nhà
            </h6>
        </div>
        <div class="option" onclick="">
            <div class="option-image-block">
                <img src="/images/glass.png" alt="coffee-cup" class="option-image" />
            </div>
            <h6>
                Tumbler collection
            </h6>
        </div>
    </div>

    <div class="menu__listing">
        <div class="container">
            <div class="row g-5">
                <?php
                foreach ($params['products'] as $param) {
                    echo '
                        <div class="col-xl-2 col-md-3 col-sm-4 col-xs-6">
                            <a href="/product?id=' . $param->id . '">
                                <div class="item-card">
                                    <img src="' . $param->image_url . '" alt=""
                                        class="item-image" />
                                    <div class="item-info">
                                        <p class="item-name">' . $param->name . '</p>
                                        <div class="item-footer">
                                            <p>' . $param->price . '</p>
                                            <div class="item-button">
                                                <img class="item-button-image"
                                                    src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIHZpZXdCb3g9IjAgMCAxNiAxNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTYuODU3MTQgNi44NTcxNFYwSDkuMTQyODZWNi44NTcxNEgxNlY5LjE0Mjg2SDkuMTQyODZWMTZINi44NTcxNFY5LjE0Mjg2SDBWNi44NTcxNEg2Ljg1NzE0WiIgZmlsbD0id2hpdGUiLz4KPC9zdmc+Cg=="
                                                    alt="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>