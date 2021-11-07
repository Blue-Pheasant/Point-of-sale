function nav() {
    var sideNav = document.getElementById("nav");
    var menu = document.getElementById("menu");
    if (sideNav.style.right == "-250px") {
        sideNav.style.right = "0";
        menu.src = "/POS-SYSTEM/image/close.png";
    } else {
        sideNav.style.right = "-250px";
        menu.src = "/POS-SYSTEM/image/menu.png";
    }
}
function show_products() {
    if (document.getElementById('sub_products').style.display == 'none') {
        document.getElementById('hr_products').style.display = 'unset';
        document.getElementById('sub_products').style.display = 'unset';
    } else {
        document.getElementById('hr_products').style.display = 'none';
        document.getElementById('sub_products').style.display = 'none';
    }
}
function show_services() {
    if (document.getElementById('sub_services').style.display == 'none') {
        document.getElementById('sub_services').style.display = 'unset';
        document.getElementById('hr_services').style.display = 'unset';
    } else {
        document.getElementById('sub_services').style.display = 'none';
        document.getElementById('hr_services').style.display = 'none';
    }
}