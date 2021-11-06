const categoryTitle = document.querySelectorAll('.category-title');
        const allCategoryPosts = document.querySelectorAll('.all');

        for (let i = 0; i < categoryTitle.length; i++) {
            categoryTitle[i].addEventListener('click', filterPosts.bind(this, categoryTitle[i]));
        }

        function filterPosts(item) {
            changeActivePosition(item);
            for (let i = 0; i < allCategoryPosts.length; i++) {
                if (allCategoryPosts[i].classList.contains(item.attributes.id.value)) {
                    allCategoryPosts[i].style.display = "block";
                } else {
                    allCategoryPosts[i].style.display = "none";
                }
            }
        }

        function changeActivePosition(activeItem) {
            for (let i = 0; i < categoryTitle.length; i++) {
                categoryTitle[i].classList.remove('active');
            }
            activeItem.classList.add('active');
        };

        function close_chi_tiet() {
            document.getElementById("chi_tiet").style.display = "none";
        }

        function show_chi_tiet(index) {
            var title = document.querySelectorAll(".all .title");
            var chi_tiet_title = document.getElementsByClassName("chi_tiet_title");
            chi_tiet_title[0].innerHTML = title[index].innerHTML;
            var weight = document.querySelectorAll(".all .weight");
            var chi_tiet_weight = document.getElementsByClassName("chi_tiet_weight");
            chi_tiet_weight[0].innerHTML = weight[index].innerHTML;
            var price = document.querySelectorAll(".all .price");
            var chi_tiet_price = document.getElementsByClassName("chi_tiet_price");
            chi_tiet_price[0].innerHTML = price[index].innerHTML;
            var hinh_anh = document.querySelectorAll(".all .thumbnail");
            var chi_tiet_img = document.getElementsByClassName("chi_tiet_img");
            chi_tiet_img[0].src = hinh_anh[index].getAttribute("src");
            document.getElementById("chi_tiet").style.display = "unset";
        }

        var add_cart = document.getElementById("add_cart");
        add_cart.addEventListener("click", function () {
            var img = document.querySelectorAll("#chi_tiet .chi_tiet_img")[0].getAttribute("src");
            var title = document.querySelectorAll("#chi_tiet .chi_tiet_title")[0].innerHTML;
            var price = parseFloat(document.querySelectorAll("#chi_tiet .chi_tiet_price")[0].innerHTML.slice(1));
            var sl = document.querySelectorAll("#chi_tiet .chi_tiet_sl")[0].value;
            //var cart_title = document.getElementsByClassName("cart_title");
            var flag = true;
            /* for (var i = 0; i < cart_title.length; i++) {
                if (title == cart_title[i].innerHTML) {
                    alert("Sản phẩm đã có trong giỏ hàng");
                    flag = false;
                }
            }
            if (flag)  */
            add_item_cart(img, title, price, sl);
            //if(cart_title.length == 0) add_item_cart(img, title, price);
        })

        function updateTongTien() {
            var item_price = document.getElementsByClassName('cart_price');
            var total = 0.0;
            for (var i = 0; i < item_price.length; i++) {
                total += parseFloat(item_price[i].innerHTML.slice(1));
            }

            var final_sum = document.getElementsByClassName('final_money')[0];
            final_sum.innerHTML = "$" + total;
            var final_sum = document.getElementsByClassName('sum')[0];
            final_sum.innerHTML = "$" + total;
        }

        function add_item_cart(img, title, price, sl) {
            var current_order = document.getElementsByClassName("current_order")[0];
            var cart_row = document.createElement("div");
            var div_img = document.createElement("div");
            var pre_div_title = document.createElement("div");
            var pre_div_price = document.createElement("div");
            var input_sl = document.createElement("div");
            var add_img = document.createElement("img");
            var add_title = document.createElement("div");
            var add_price = document.createElement("div");
            var add_button = document.createElement("input");
            cart_row.classList.add("cart_row");
            cart_row.classList.add("col-xl-12");
            div_img.classList.add("div_img");
            pre_div_title.classList.add("pre_div_title");
            pre_div_price.classList.add("pre_div_price");
            input_sl.innerHTML = sl;
            input_sl.classList.add("sl");
            add_img.classList.add("cart_img");
            add_img.src = img;
            add_title.classList.add("cart_title");
            add_title.innerHTML = title;
            add_price.classList.add("cart_price");
            add_price.innerHTML = "$" + price * sl;
            add_button.type = "button";
            add_button.classList.add("add_button");
            add_button.value = "Xóa";
            div_img.appendChild(add_img);
            pre_div_title.appendChild(add_title);
            pre_div_price.appendChild(add_price);
            cart_row.appendChild(div_img);
            cart_row.appendChild(pre_div_title);
            cart_row.appendChild(input_sl);
            cart_row.appendChild(pre_div_price);
            cart_row.appendChild(add_button);
            current_order.appendChild(cart_row);
            var remove_cart = document.getElementsByClassName('add_button');
            var item_price = document.getElementsByClassName('cart_price');
            var total = 0.0;
            for (var i = 0; i < item_price.length; i++) {
                total += parseFloat(item_price[i].innerHTML.slice(1));
            }

            var final_sum = document.getElementsByClassName('final_money')[0];
            final_sum.innerHTML = "$" + total;
            var final_sum = document.getElementsByClassName('sum')[0];
            final_sum.innerHTML = "$" + total;

            for (var i = 0; i < remove_cart.length; i++) {
                var button = remove_cart[i]
                button.addEventListener("click", function () {
                    var button_remove = event.target
                    button_remove.parentElement.remove()
                    updateTongTien()
                })
            }

            
            /* var input_so_luong = document.getElementsByClassName("sl");
            var pre_sl = [];
            var pre_cost = [];
            for (var j = 0; j < input_so_luong.length; j++) {
                var so_luong = input_so_luong[j];
                so_luong.addEventListener('input', function () {
                    if (so_luong.value < pre_sl) {
                        var x = pre_cost - (pre_cost / pre_sl);
                        so_luong.parentElement.getElementsByClassName("cart_price")[0].innerHTML =
                            "$" + x;
                    }
                    else if (so_luong.value != 1) {
                        so_luong.parentElement.getElementsByClassName("cart_price")[0].innerHTML =
                            "$" + so_luong.value / (so_luong.value - 1) * parseFloat(so_luong.parentElement.getElementsByClassName("cart_price")[0].innerHTML.slice(1));
                    }//var cart_price = input_so_luong[j].parentElement.getElementsByClassName("cart_price")[0];
                    //cart_price.innerHTML = "cc";
                    pre_sl[j] = input_so_luong[j].value;
                    pre_cost[j] = parseFloat(input_so_luong[j].parentElement.getElementsByClassName("cart_price")[0].innerHTML.slice(1));
                    //cart_price.innerHTML = input_so_luong[j].value * parseInt(cart_price.innerHTML.slice(1));
                });
            } */
        }