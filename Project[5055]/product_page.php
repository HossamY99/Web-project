<?php
if(!isset($_GET["id"]) || empty($_GET["id"])) {
    dieWithMessage("Something went wrong."); // Bad parameters
}
// Get the product info
$product_id = $_GET["id"];
addToLastSeen($product_id);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/product_page.css">
        <title>Project Mart</title>
    </head>
    <?php include "header.php";
        include "connect_db.php";
        $sql = $db->prepare("SELECT product.id, product_name, price, quantity, subcategory_id, subcategory_name
                            FROM product, subcategory
                            WHERE product.id = :product_id AND subcategory_id = subcategory.id;");
        $sql->bindParam(':product_id', $product_id);
        $sql->execute();
        $results = $sql->fetchAll(PDO::FETCH_ASSOC);
        if(!$results) {
            dieWithMessage("No results found.");
        }
        include "display_products.php";
        // Show the product
        displayProducts($results);
    ?>
    </body>
</html>

<?php
// Adds the product to the last_seen cookie and updates the last_seen_index cookie
function addToLastSeen($product_id) {
    if(!isset($_COOKIE["last_seen"])) {
        // Cookie expires after 30 days
        setcookie("last_seen[0]", $product_id, time() + (86400 * 30), "/");
        setcookie("last_seen_count", 1, time() + (86400 * 30), "/");
    }
    // Cookie is set, add the product, replacing the oldest one if necessary
    elseif($_COOKIE["last_seen_count"] < 5) {
        $index = $_COOKIE["last_seen_count"];
        $cookie_name = "last_seen[". $index ."]";
        setcookie($cookie_name, $product_id, time() + (86400 * 30), "/");
        // Update the index cookie
        $index++;
        setcookie("last_seen_count", $index, time() + (86400 * 30), "/");
    }
    else {
        $last_seen = $_COOKIE["last_seen"];

        array_shift($last_seen);
        foreach($last_seen as $index => $id) {
            $cookie_name = "last_seen[". $index ."]";
            setcookie($cookie_name, $id, time() + (86400 * 30), "/");
        }
        $cookie_name = "last_seen[". 4 ."]";
        setcookie($cookie_name, $product_id, time() + (86400 * 30), "/");

        // Update the index cookie
        $last_seen = $_COOKIE["last_seen"];
        $index = $_COOKIE["last_seen_count"];
        $index++;
        setcookie("last_seen_count", $index, time() + (86400 * 30), "/");
        $last_seen = $_COOKIE["last_seen"];
    }
}

// Shows a message and links then dies.
function dieWithMessage($message) {
    echo $message;
    echo '<br><a href="'. $_SERVER['HTTP_REFERER'] .'">Click here to go back</a>';
    die();
}
?>