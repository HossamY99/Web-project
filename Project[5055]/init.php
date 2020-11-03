<?php
session_start();
if(isset($_SESSION["user_id"])) {
    $logged_in = true;
    $first_name = $_SESSION["first_name"];
}
else {
    $logged_in = false;
    session_destroy();
}
?>