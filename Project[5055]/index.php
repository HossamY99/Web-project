<!-- TODO: We should probably also add a footer. -->
    <?php 
    include "header.php";
    include "sorting_options.php";
    ?>
        <h1 class="heading">Popular</h1>

        <?php
        include "connect_db.php";
        // Can't use bindParam here, so sanitized the input previously
        $sql = $db->prepare("SELECT id, product_name, price
                             FROM product
                             WHERE quantity > 0
                             ORDER BY $order $sort;");
        $sql->execute();
        $results = $sql->fetchAll(PDO::FETCH_ASSOC);

        include "display_products.php";
        displayProducts($results);

        $db = NULL; // Close the connection
        ?>

        <?php
        if(isset($_COOKIE["last_seen"])) {
            $last_seen = $_COOKIE["last_seen"];
            $results = array();
            echo '<h1>Last Seen:</h1>';
            foreach($last_seen as $index => $id) {
                array_push($results, getProductInfo($id)[0]);
            }
            displayProducts($results);
        }

        function getProductInfo($product_id) {
            include "connect_db.php";
            $sql = $db->prepare("SELECT id, product_name, price
                                 FROM product
                                 WHERE id = :product_id;");
            $sql->bindParam(':product_id', $product_id);
            $sql->execute();
            $results = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        }
        ?>
    </body>
</html>