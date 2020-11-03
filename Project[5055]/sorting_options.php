<?php
$order = "product_name";
$sort = "ASC";
if(isset($_GET["sort-btn"])) {
    if(isset($_GET["order"]) && !empty($_GET["order"])) {
        // Sanitize the input, only accept price, otherwise use product_name
        if($_GET["order"] == "price") {
            $order = "price";
        }
    }
    if(isset($_GET["sort"]) && !empty($_GET["sort"])) {
        if($_GET["sort"] == "DESC") {
            $sort = $_GET["sort"];
        }
    }
}
?>
<!-- Sorting options -->
<form class="rf" method="get">
    <?php
    if(isset($subcategory)) {
        echo '<input type="hidden" name="subcategory" value="'. $subcategory .'">';
    }
    ?>
    <select name="order" id="order">
        <?php
        if($order == "price") {
            echo '<option value="product_name">Name</option>
                    <option value="price" selected>Price</option>';
        }
        else {
            echo '<option value="product_name" selected>Name</option>
            <option value="price">Price</option>';
        }
        ?>
    </select>
    <select name="sort" id="sort">
        <?php
        if($sort == "DESC") {
            echo '<option value="ASC">Ascending</option>
                    <option value="DESC" selected>Decending</option>';
        }
        else {
            echo '<option value="ASC" selected>Ascending</option>
                    <option value="DESC">Decending</option>';
        }
        ?>
    </select>
    <input type="submit" name="sort-btn" value="Sort">
</form>