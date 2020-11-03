<?php
if(!isset($_POST["action"]) || empty($_POST["action"])) {
    dieWithMessage('Parameter "action" not set. Please try again.');
}
$action = $_POST["action"];

if(!isset($_POST["product_id"]) || empty($_POST["product_id"])) {
    dieWithMessage('Parameter "product_id" not set. Please try again.');
}
$product_id = $_POST["product_id"];

// Check if logged in
session_start();
$logged_in = false;
if(isset($_SESSION["user_id"])) {
    $logged_in = true;
    $user_id = $_SESSION["user_id"];
}

if($action == "add") {
    if($logged_in) {
        addToDatabase($user_id, $product_id);
    }
    else { // Not logged in
        addToCookie($product_id);
    }
}

// Adds the product to the cart cookie
function addToCookie($product_id) {
    $cookie_name = "cart[". $product_id ."]";
    // If the product is already in the cart, increase its qty by 1
    if(isset($_COOKIE["cart"][$product_id])) {
        $qty = $_COOKIE["cart"][$product_id];
        $split =   explode(":", $qty);
        $newQty = intval($split[0]) + 1;
        $qty = $newQty .":". $split[1];
        // Cookie expires after 30 days
        setcookie($cookie_name, $qty, time() + (86400 * 30), "/");
    }
    else { // Not in the cart, add it with qty = 1
        // Cookie expires after 30 days
        setcookie($cookie_name, "1:0", time() + (86400 * 30), "/");
    }
    echo "Product added to cart.";
    die();
}

/* Adds the product to the user's cart in the database
   If it already exists, increments its quantity by 1 */
function addToDatabase($user_id, $product_id) {
    include "connect_db.php";
    $sql = $db->prepare("INSERT INTO cart (user_id, product_id, qty)
                         VALUES (:user_id, :product_id, 1)
                         ON DUPLICATE KEY UPDATE qty = qty + 1;");
    $sql->bindParam(':user_id', $user_id);
    $sql->bindParam(':product_id', $product_id);

    try {
        $sql->execute();
        echo "Product added to cart.";
        die();
    } catch(PDOException $error) { // Error inserting the user
        var_dump($sql);
        var_dump($error->getMessage());
        die();
    }
}

// Shows a message and links then dies.
function dieWithMessage($message) {
    echo $message;
    echo '<br><a href="signup.php">Click here to go back</a>';
    die();
}