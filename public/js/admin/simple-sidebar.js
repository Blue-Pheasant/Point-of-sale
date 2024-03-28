$("#menu-toggle").click(function(e){
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
    $(this).children().toggleClass("fa-arrow-left fa-bars");
});