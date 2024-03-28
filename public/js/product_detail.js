function increaseQuantity() {
    var currentQuantity = parseInt(document.getElementById("product-quantity").value,10);
    if(currentQuantity == 1 ){
        document.getElementById("decrease-quantity-button").disabled = false;
        document.getElementById("decrease-quantity-button").classList.remove("item-button-disabled");
    }
    document.getElementById("product-quantity").value = currentQuantity + 1;
}

function decreaseQuantity() {
    var currentQuantity = parseInt(document.getElementById("product-quantity").value,10);
    if(currentQuantity == 2 ){
       document.getElementById("decrease-quantity-button").disabled = true;
       document.getElementById("decrease-quantity-button").classList.add("item-button-disabled");
    }
    document.getElementById("product-quantity").value = currentQuantity - 1;
};

function numberWithCommas() {
    var price = document.getElementsByClassName("price").innerHTML;
    console.log(price)
    document.getElementsByClassName("price").innerHTML = price.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}


function getSize() {
    var radios = document.getElementsByName('exampleRadios');
    var size = 1;
    for (var i = 0, length = radios.length; i < length; i++) {
      if (radios[i].checked) {
        if(radios[i].value == "option1") {
            size = 1;
        } else if(radios[i].value == "option2") {
            size = 2;
        } else {
            size = 3
        }
        break;
      }
    }
    return size;
}

function updatePrice() {
    var currentQuantity = parseInt(document.getElementById("product-quantity").innerHTML,10);
    var currentPrice = parseInt(document.getElementsById("price").innerHTML, 10);
    var currentSize = getSize();
    if(currentSize == 2) {
        currentPrice += 3000;
    }
    else if(currentPrice == 3) {
        currentPrice += 6000;
    }
    return currentPrice*currentQuantity;
};
