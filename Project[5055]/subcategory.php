<?php
if(!isset($_GET["subcategory"]) || empty($_GET["subcategory"])) { // Bad parameter
    header("Location: index.php");
    die();
}   
$subcategory = $_GET["subcategory"];
?>
    <?php 
    include "header.php";
    include "sorting_options.php";
    ?>
        <h1 class="heading">
            <?= $subcategory; ?>
        </h1>
        <?php
        include "connect_db.php";
        // $order and $sort were already sanitized
        $sql = $db->prepare ("SELECT product.id, product_name, price
                              FROM product, subcategory
                              WHERE subcategory_name = :subcategory
                                AND product.subcategory_id = subcategory.id
                                AND quantity > 0
                              ORDER BY $order $sort;");
        $sql->bindParam(':subcategory', $subcategory);
        $sql->execute();
        $results = $sql->fetchAll(PDO::FETCH_ASSOC);

        include "display_products.php";
        displayProducts($results);

        $db = NULL; // Close the connection
        ?>
    </body>
</html>