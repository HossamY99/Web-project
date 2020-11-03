<?php
include "connect_db.php";

checkParams();
$email = $_POST["email"];
$pwd = $_POST["password"];

$sql = $db->prepare("SELECT id, email, pwd, first_name, is_admin
                     FROM user
                     WHERE email = :email AND pwd = :pwd");
$sql->bindParam(":email", $email);
$sql->bindParam(":pwd", md5($pwd));
$sql->execute();

$results = $sql->fetchAll(PDO::FETCH_ASSOC);

if(!$results) { // Login failed
    header("Location: login.php?login=failed");
    die();
}
else { // Login successful
    session_start();
    $_SESSION["user_id"] = $results[0]["id"];
    $_SESSION["first_name"] = $results[0]["first_name"];
    $_SESSION["admin"] = $results[0]["is_admin"];

    if(isset($_COOKIE["cart"]) && !empty($_COOKIE["cart"])) {
        addCartToDatabase($results[0]["id"]);
    }
    if($_POST["referrer"] == "checkout") {
        header("Location: cart.php");
    }
    else {
        header("Location: index.php?wein");
    }
    die();
}

/* TODO: Add click here to signup */

/* Checks that the email and password parameters were entered properly.
   Kills the script if they were not. */
function checkParams() {
    /* TODO: validate email with regex both client-side and server-side.
       Validate password server-side only to lessen load on DB.
       (If password should contain a capital letter and it doesn't,
       we can assume it's a wrong password) */
    if(!isset($_POST["email"]) || empty($_POST["email"])) {
        dieWithMessage("Please enter a valid email.");
    }
    if(!isset($_POST["password"]) || empty(["password"])) {
        dieWithMessage("Please enter a valid password.");
    }
}

// Adds the items in the cart cookie to the database
function addCartToDatabase($user_id) {
    $cart = $_COOKIE["cart"];
    foreach($cart as $product_id => $qty) {
        $split = explode(":", $qty);
        addToDatabase($user_id, $product_id, $split[0]);
    }
}

/* Adds the product to the user's cart in the database
If it already exists, increments its quantity by 1 */
function addToDatabase($user_id, $product_id, $qty) {
    include "connect_db.php";
    $sql = $db->prepare("INSERT INTO cart (user_id, product_id, qty)
                         VALUES (:user_id, :product_id, :qty)
                         ON DUPLICATE KEY UPDATE qty = qty + 1;");
    $sql->bindParam(':user_id', $user_id);
    $sql->bindParam(':product_id', $product_id);
    $sql->bindParam(':qty', $qty);

    try {
        $sql->execute();
    } catch(PDOException $error) { // Error inserting the user
        dieWithMessage($sql ."<br>". $error->getMessage());
    }
}

// Shows a message then redirects the user back to the login page
function dieWithMessage($message) {
    echo $message;
    echo '<br><a href="login.php">Click here to go back</a>';
    die();
}
?>