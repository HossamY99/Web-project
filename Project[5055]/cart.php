<?php
include "header.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/cart.css?ver=<?php echo filemtime('css/cart.css'); ?>">
    <script src="js/newcart.js"></script>
</head>

<body>
    <div id="cart_page_body">
    <p id="empty-alert"></p>
    <table id="cart_summary">
        <thead></thead>
        <tbody></tbody>
        <tfoot></tfoot>
    </table>
    </div>


    <script>
        let products = <?= $cart ?>;
        let isLogged = <?= $isLoggedin ?>;
        create_table();
    </script>
</body>

</html>