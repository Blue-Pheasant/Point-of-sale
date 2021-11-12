<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-
        DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns"
        crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cookie&family=Dancing+Script&family=Raleway:ital@1&family=Zen+Antique+Soft&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../public/css/pos.css">
</head>

<body>
    <div id="wrapper">
        <div class="wrap container">
            <div class="row row_1">
                <div class="col-xl-9 col-md-9 product">
                    <div class="container list_product">
                        <div class="row">
                            <div class="col-xl-12 header">
                                <a href="index.php"><img src="../../public/image/dish.png" alt=""
                                        style="float: left;"></a>
                                <div class="thuongHieu">Buy Me</div>
                            </div>
                            <div class="col-md-12 category-head">
                                <nav>
                                    <div class="category-title active" id="all">
                                        <a>All</a>
                                        <span><i class="fas fa-border-all"></i></span>
                                    </div>
                                    <div class="category-title" id="food">
                                        <a>Foods</a>
                                        <span><i class="fas fa-theater-masks"></i></span>
                                    </div>
                                    <div class="category-title" id="coldrink">
                                        <a>Cold Drinks</a>
                                        <span><i class="fas fa-landmark"></i></span>
                                    </div>
                                    <div class="category-title" id="hotdrink">
                                        <a>Hot Drinks</a>
                                        <span><i class="fas fa-chart-area"></i></span>
                                    </div>
                                    <div class="category-title" id="other">
                                        <a>Other</a>
                                        <span><i class="fas fa-coins"></i></span>
                                    </div>
                                </nav>
                            </div>
                        </div>
                        <div class="row danh_muc">
                            <div class="col-xl-3 col-sm-6 col-6 all other" onclick="show_chi_tiet(0);">
                                <div class="bg_sp">
                                    <div class="title cc">Other 1</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(1);">
                                <div class="bg_sp">
                                    <div class="title">Food 1</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_1.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(2);">
                                <div class="bg_sp">
                                    <div class="title">Food 2</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_4.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(3);">
                                <div class="bg_sp">
                                    <div class="title">Food 3</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_4.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all hotdrink" onclick="show_chi_tiet(4);">
                                <div class="bg_sp">
                                    <div class="title">Hotdrink 1</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all other" onclick="show_chi_tiet(5);">
                                <div class="bg_sp">
                                    <div class="title">Other 2</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all coldrink" onclick="show_chi_tiet(6);">
                                <div class="bg_sp">
                                    <div class="title">Coldrink 1</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(7);">
                                <div class="bg_sp">
                                    <div class="title">Food 4</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_4.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all coldrink" onclick="show_chi_tiet(8);">
                                <div class="bg_sp">
                                    <div class="title">Coldrink 2</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_3.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all hotdrink" onclick="show_chi_tiet(9);">
                                <div class="bg_sp">
                                    <div class="title">Hotdrink 2</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all coldrink" onclick="show_chi_tiet(10);">
                                <div class="bg_sp">
                                    <div class="title">Coldrink 3</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_1.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all coldrink" onclick="show_chi_tiet(11);">
                                <div class="bg_sp">
                                    <div class="title">Coldrink 4</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(12);">
                                <div class="bg_sp">
                                    <div class="title">Food 5</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_1.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(13);">
                                <div class="bg_sp">
                                    <div class="title">Food 6</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all other" onclick="show_chi_tiet(14);">
                                <div class="bg_sp">
                                    <div class="title">Other 3</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_3.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all other" onclick="show_chi_tiet(15);">
                                <div class="bg_sp">
                                    <div class="title">Other 4</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_4.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all other" onclick="show_chi_tiet(16);">
                                <div class="bg_sp">
                                    <div class="title">Other 1</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(17);">
                                <div class="bg_sp">
                                    <div class="title">Food 1</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_1.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(18);">
                                <div class="bg_sp">
                                    <div class="title">Food 2</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_4.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(19);">
                                <div class="bg_sp">
                                    <div class="title">Food 3</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_4.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all hotdrink" onclick="show_chi_tiet(20);">
                                <div class="bg_sp">
                                    <div class="title">Hotdrink 1</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all other" onclick="show_chi_tiet(21);">
                                <div class="bg_sp">
                                    <div class="title">Other 2</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all coldrink" onclick="show_chi_tiet(22);">
                                <div class="bg_sp">
                                    <div class="title">Coldrink 1</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(23);">
                                <div class="bg_sp">
                                    <div class="title">Food 4</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_4.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all coldrink" onclick="show_chi_tiet(24);">
                                <div class="bg_sp">
                                    <div class="title">Coldrink 2</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_3.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all hotdrink" onclick="show_chi_tiet(25);">
                                <div class="bg_sp">
                                    <div class="title">Hotdrink 2</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all coldrink" onclick="show_chi_tiet(26);">
                                <div class="bg_sp">
                                    <div class="title">Coldrink 3</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_1.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all coldrink" onclick="show_chi_tiet(27);">
                                <div class="bg_sp">
                                    <div class="title">Coldrink 4</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/drink_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(28);">
                                <div class="bg_sp">
                                    <div class="title">Food 5</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_1.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all food" onclick="show_chi_tiet(29);">
                                <div class="bg_sp">
                                    <div class="title">Food 6</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_2.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all other" onclick="show_chi_tiet(30);">
                                <div class="bg_sp">
                                    <div class="title">Other 3</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_3.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-sm-6 col-6 all other" onclick="show_chi_tiet(31);">
                                <div class="bg_sp">
                                    <div class="title">Chả cá chiên giòn</div>
                                    <div class="weight">150g</div>
                                    <div class="dish">
                                        <div class="price">$1.75</div>
                                        <img src="../../public/image/dish_4.jpg" alt="" class="thumbnail">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="chi_tiet">
                            <table>
                                <tr>
                                    <td></td>
                                    <td class="close"><a onclick="close_chi_tiet();">x</a></td>
                                </tr>
                                <tr>
                                    <td rowspan="4"><img src="" alt="" class="chi_tiet_img"></td>
                                    <td>
                                        <div class="chi_tiet_title"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="chi_tiet_weight"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="chi_tiet_price"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="number" class="chi_tiet_sl" value="1">
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><button id="add_cart" type='button'>Add To Cart</button></td>
                                </tr>
                            </table>
                        </div>
                        <div class="row nv_thungan">
                            <div class="col-xl-12">
                                <img src="../../public/image/avatar.png" alt=""> Nguyễn Văn A
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-3 cart">
                    <div class="container list_cart">
                        <div class="row current_order">
                            <div class="col-xl-12">
                                Current Order
                            </div>
                        </div>
                        <div class="row bg_sum">
                            <div class="col-xl-9">Sum:</div>
                            <div class="col-xl-3 sum">1</div>
                            <div class="col-xl-9">Discount:</div>
                            <div class="col-xl-3 discount">0</div>
                            <hr>
                            <div class="col xl-9 final_sum">
                                Tổng tiền
                            </div>
                            <div class="col-xl-3 final_money"></div>
                        </div>
                        <div class="row wrap_pay">
                            <div class="pre_pay">
                                <button class="pay">Thanh toán</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../public/js/pos.js"></script>
</body>

</html>