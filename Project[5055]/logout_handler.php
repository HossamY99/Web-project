<?php
session_start();
session_regenerate_id(TRUE);
session_destroy();

header("Location: index.php");
die();
?>