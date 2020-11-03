<?php
$server = "localhost:3308";
$dbname = "project";
$user = "root";
$password = "";

$db = new PDO("mysql:host=$server;dbname=$dbname", $user, $password);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
