function nav() {
    var sideNav = document.getElementById("nav");
    var menu = document.getElementById("menu");
    if (sideNav.style.right == "-250px") {
        sideNav.style.right = "0";
        menu.src = "../image/close.png";
    } else {
        sideNav.style.right = "-250px";
        menu.src = "../../image/menu.png";
    }
}

/* var nav_bar = document.querySelectorAll('#banner .nav-item');

for (let i = 0; i < nav_bar.length; i++) {
    nav_bar[i].addEventListener('click', filterPosts.bind(this, nav_bar[i]));
}

function filterPosts(item) {
    changeActivePosition(item);
}

function changeActivePosition(activeItem) {
    for (let i = 0; i < nav_bar.length; i++) {
        nav_bar[i].classList.remove('active');
    }
    activeItem.classList.add('active');
} */

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