<!-- Contains the header and navbars.
    Linked in all pages to reduce redundancy and make editing the header easier. -->
<?php
include "init.php";
include "cart_data.php";
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="css/popup_cart.css">
        <script src="js/jquery-3.5.1.min.js"></script>
        <script src="js/add_cart.js"></script>
        <script src="js/popupcart.js"></script>
        <script src="js/newcart.js"></script>
        <title>Project Mart</title>
    </head>
<body>
    <div class="top">
        <div id="cart-btn-container">            
            <div id="popup-cart"></div>
        </div>
        <div class="topnav">
            <a class="active" href="index.php">Home</a>
            <a href="#news">News</a>
            <a href="#contact">Contact</a>
            <a href="#about">About</a>
            <span class="right">
            <a id="cart-trigger" href="cart.php">Cart</a>
            <?php
            if(!$logged_in) {
                echo
                '<a href="signup.php">Sign Up</a>
                <a href="login.php">Log In</a>';
            }
            else { // Logged in
                $id = $_SESSION["user_id"];
                echo
                '<a href="logout_handler.php">Log Out</a>';
            }
            ?>
            </span>
            <span class="searchdiv">
                <form class="searchbar">
                    <input type="text" name="search" placeholder="Search..">
                </form>
            </span>
            <?php
            if($logged_in) {
                echo
                '<h3>Welcome, '. $first_name .'!</h3>';
            }
            ?>
    <!-- TODO: Generate these categories with php from the DB. -->
        </div>
    </div>
    <div id="header">
        <h1>Blue Gear</h1>
    </div>
    <div class="navbar">
        <?php
        include "connect_db.php";
        $sql = $db->prepare("SELECT category_name, subcategory_name
                             FROM category
                             LEFT JOIN subcategory
                             ON category.id = subcategory.category_id;");
        $sql->execute();
        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        
        // Array to keep track of echo'd categories
        $seen_categories = array();
        $first_row = true;
        foreach($results as $row) {
            $name = $row["category_name"];
            if($first_row) {
                array_push($seen_categories, $name);
                echo
                '<div class="dropdown">
                    <button class="dropbtn">
                        '. $row["category_name"] .'
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <div class="row">';
                $first_row = false;
            }
            elseif(!in_array($name, $seen_categories)) {
                array_push($seen_categories, $name);
                echo
                '</div></div></div>
                 <div class="dropdown">
                    <button class="dropbtn">
                        '. $row["category_name"] .'
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <div class="row">';
            }
            echo
            '<div class="column">
                <a href="subcategory.php?subcategory='. $row["subcategory_name"] .'">
                    '. $row["subcategory_name"] .'
                </a>
            </div>';
        }
        echo '</div></div></div>';
        ?>
    </div>

    <script>
        let trigger = document.getElementById("cart-trigger"),
            popupCart = document.getElementById("popup-cart"),
            data = <?= $cart ?>;

        renderCartItems(data);
        trigger.onmouseover = () => renderCartItems(data);
        trigger.onmouseout = () => toggleCart(false);
    </script>