<?php

use function PHPSTORM_META\type;

if($logged_in) {
    $isLoggedin = 1;
    $cart = getCartDB($_SESSION["user_id"]);
}
elseif(isset($_COOKIE["cart"])){
    $isLoggedin = 0;
    $array = json_decode($_COOKIE["cart"], true);
    if(count($array) == 0) $cart = json_encode(array());
    else $cart = cookieToJson($array);
}
else { 
    $cart = json_encode(array());
}

// $dummyCookie = [1=>"17:0", 2=>"5:0", 3=>"5:0"]; //for testing purposes. suggested cookie format.
// $cart = cookieToJson($dummyCookie);
// setcookie("cart", json_encode($dummyCookie));

// $cart = getCartDB(300);
// $discount = getVoucherDiscount(132465789123);

if(isset($_REQUEST["voucher_code"])){
    header('Content-type: application/json');
    $voucher_code = $_REQUEST["voucher_code"];
    $discount = getVoucherDiscount($voucher_code);

    if($logged_in){
        editUpdatedPriceDB(($discount[0]['discount']));
        // echo json_encode($discount);
    }else{
        print_r($discount);
        editUpdatedPriceCookie(json_decode($_COOKIE["cart"], true), $discount[0]['discount']);
    }
}

if(isset($_REQUEST["delete"])){
    header('Content-type: application/json');
    $id = $_REQUEST["delete"];
    if($logged_in) deleteCartProductDB($id);
    else deleteCartProductCookie(json_decode($_COOKIE["cart"], true), $id);

}

if(isset($_REQUEST["product_id"]) && isset($_REQUEST["qty"])){
    header('Content-type: application/json');
    $id = $_REQUEST["product_id"];
    $qty = $_REQUEST["qty"];
    if($logged_in){
        update_qty_DB($id, $qty);
    }else{
        update_qty_cookie(json_decode($_COOKIE["cart"], true), $id, $qty);
    }
    
}

function getCartDB($id)
{
    include "connect_db.php";
    $sql = $db->prepare("SELECT p.*, c.*
                        FROM cart c, product p
                        WHERE c.user_id = :id AND p.id = c.product_id");
    $sql->bindParam(':id', $id);
    $sql->execute();
    $products = $sql->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return json_encode($products);
}

function cookieToJson($cookie)
{
    include "connect_db.php";
    $where = "WHERE ";
    $quantities = [];
    $updatedPrices = [];
    
    foreach ($cookie as $id => $item) {
        $arr = explode(":", $item);
        $where = $where . "p.id = $id OR ";
        $quantities[] = $arr[0];
        $updatedPrices[] = $arr[1];
    }
    $where = substr($where, 0, strlen($where) - 3);
    if($where == "WHE") $where = "";
    $sql = $db->prepare("SELECT p.*
                        FROM product p
                        $where");
    $sql->execute();
    $products = $sql->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;

    for ($i = 0; $i < count($quantities); $i++) {
        $products[$i]["qty"] = $quantities[$i];
        $products[$i]["updated_price"] = $updatedPrices[$i];
    }
    return json_encode($products);
}

function deleteCartProductDB($id){
    include "connect_db.php";
    $sql = $db->prepare("DELETE FROM cart
                         WHERE id = :id");
    return $sql->execute([':id' => $id]); 
}

function deleteCartProductCookie($cart, $prdId) {
    unset($cart[$prdId]);
    echo("<p>SET COOKIE:</p> ");
    
    setcookie("cart", json_encode($cart));
}

function compareIDs($arrayId, $prdId) {
    return $arrayId == $prdId;
}

function getVoucherDiscount($voucher_code){
    include "connect_db.php";
    $sql = $db->prepare("SELECT v.discount
                        FROM  voucher v
                        WHERE voucher_code = :voucher_code ");
    $sql->bindParam(':voucher_code', $voucher_code);
    $sql->execute();
    $discount = $sql->fetchAll(PDO::FETCH_ASSOC);
    $db = NULL;
    return ($discount);
}

function editUpdatedPriceDB($dis){
    include "connect_db.php";
    $sql = $db->prepare("UPDATE cart
                         SET  cart.updated_price = (
                             SELECT price
                             FROM product
                             WHERE cart.product_id = product.id
                         ) * (1 - $dis/100)");
    return $sql->execute();
}

function update_qty_DB($id, $qty){
    include "connect_db.php";
    $sql = $db->prepare("UPDATE cart
                         SET cart.qty = $qty
                         WHERE cart.id = $id");
    return $sql->execute();
}

function update_qty_cookie($cart, $prdId, $qty){
    $arr = explode(":",  $cart[$prdId]);

    $tempPrd = $qty.":".$arr[1];
    $cart[$prdId] = $tempPrd;
    
    setcookie("cart", json_encode($cart));
}

function editUpdatedPriceCookie($cart, $dis){
    include "connect_db.php";
    print_r($cart);
    echo "before loop\n";
    foreach($cart as $key => $item) {
        $info = explode(":", $item);
        $sql = $db->prepare("SELECT price
                                FROM product
                                WHERE id = $key
                            ");
        $sql->execute();
        $result = $sql->fetchAll();
        $price = $result[0][0];

        $info[1] = $price * (1 - $dis/100);
        $cart[$key] = implode(":", $info);
    }
    setcookie("cart", json_encode($cart));
}