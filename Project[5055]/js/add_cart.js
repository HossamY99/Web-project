$(document).ready(function(){
    var add_buttons = $(".add-to-cart");
    if(add_buttons) {
        add_buttons.on("click", function() {
            addToCart(this.value);
        });
    }
});

function addToCart(product_id) {
    $.post("add_to_cart_handler.php", {
        action: "add",
        product_id: product_id
    },
    function(message) {
        if(message) {
            alert(message);
        }
    });
}