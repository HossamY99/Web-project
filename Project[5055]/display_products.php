<?php
function displayProducts($results) {
    if(count($results) == 0) {
        echo "No results found.";
    }
    else {
        echo '<div class="flex-container">';
        // Output the data from each row as a product
        foreach($results as $row) {
            $product_id = $row["id"];
            $product_name = $row["product_name"];
            
            // Compute the src of the image
            $img_src = strtolower($product_name);
            $img_src = str_replace(" ", "_", $img_src);
            $img_src = "img/product/". $img_src .".png";

            $product_url = "product_page.php?id=". $product_id;
            $price = $row["price"];
            echo
            '<div>
                <a href="'. $product_url .'">
                    <img class="product-img" src="'. $img_src .'" alt="'. $product_name .'">
                </a><br>
                <a href="'. $product_url .'">
                    <p class="names">'. $product_name .'</p>
                </a>
                <p class="price">$'. $price .'</p>
                <button type="button" value="'. $product_id .'" class="btn add-to-cart">Add to cart</button>
            </div>';
        }
        echo '</div>';
    }
}
?>