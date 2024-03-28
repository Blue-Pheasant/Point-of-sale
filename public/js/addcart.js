var addCart = document.getElementById("addCart");
    addCart.addEventListener("click", function () {
        var img = document.querySelectorAll(".main-image")[0].getAttribute("src");
        var title = document.querySelectorAll(".product-detail-name")[0].innerHTML;
        var price = parseFloat(document.querySelectorAll(".price")[0].innerHTML.slice(1));
        var quantity = parseInt(document.getElementById("product-quantity").innerHTML,10);
        insertCart(img, title, price, quantity);
    })

function updateTotalPrice() {
    var itemPrice = document.getElementsByClassName('cartPrice');
    var total = 0;
    for (var i = 0; i < itemPrice.length; i++) {
        total += parseFloat(itemPrice[i].innerHTML.slice(1));
    }

    var totalPrice = document.getElementsByClassName('totalPrice')[0];
    totalPrice.innerHTML = "$" + total;
    var totalPrice = document.getElementsByClassName('sum')[0];
    totalPrice.innerHTML = "$" + total;
}

function insertCart(img, title, price, quantity) {
    var currentOrder = document.getElementsByClassName("currentOrder")[0];
    var cartRow = document.createElement("div");
    var divImg = document.createElement("div");
    var title = document.createElement("div");
    var title = document.createElement("div");
    var inputQuantity = document.createElement("div");
    var addImage = document.createElement("img");
    var addTitle = document.createElement("div");
    var addPrice = document.createElement("div");
    var addButton = document.createElement("input");
    cartRow.classList.add("cartRow");
    cartRow.classList.add("col-xl-12");
    divImg.classList.add("divImg");
    title.classList.add("title");
    title.classList.add("title");
    inputQuantity.innerHTML = quantity;
    inputQuantity.classList.add("quantity");
    addImage.classList.add("cartImg");
    addImage.src = img;
    addTitle.classList.add("cartTitle");
    addTitle.innerHTML = title;
    addPrice.classList.add("cartPrice");
    addPrice.innerHTML = "$" + price * quantity;
    addButton.type = "button";
    addButton.classList.add("addButton");
    addButton.value = "Xóa";
    divImg.appendChild(addImage);
    title.appendChild(addTitle);
    title.appendChild(addPrice);
    cartRow.appendChild(divImg);
    cartRow.appendChild(title);
    cartRow.appendChild(inputQuantity);
    cartRow.appendChild(title);
    cartRow.appendChild(addButton);
    currentOrder.appendChild(cartRow);
    var removeItems = document.getElementsByClassName('addButton');
    var itemPrice = document.getElementsByClassName('cartPrice');
    var total = 0.0;
    
    for (var i = 0; i < itemPrice.length; i++) {
        total += parseFloat(itemPrice[i].innerHTML.slice(1));
    }
    
    var totalPrice = document.getElementsByClassName('totalPrice')[0];
    totalPrice.innerHTML = "$" + total;
    var totalPrice = document.getElementsByClassName('sum')[0];
    totalPrice.innerHTML = "$" + total;
    
    for (var i = 0; i < removeItems.length; i++) {
        var button = removeItems[i];
        button.addEventListener("click", function () {
            var button_remove = event.target;
            button_remove.parentElement.remove();
            updateTotalPrice();
        })
    }
};


