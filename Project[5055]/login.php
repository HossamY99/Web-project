<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/popup_cart.css">
        <script src="js/popupcart.js"></script>
        <title>Project Mart</title>
    </head>
    
    <?php include "header.php"; ?>
        <form action="login_handler.php" method="post">
            <label for="email">Email:</label><br>
            <input type="text" name="email" required><br>
            <label for="password">Password:</label><br>
            <input type="password" name="password" required><br><br>

            <input type="submit" name="login" value="Log In"><br>

            <?php
            if(isset($_GET["login"]) && $_GET["login"] == "failed") {
                echo "Wrong email or password!";
            }
            ?>

            <!-- TODO: Add a "Forgot your password?" link and a way to reset password. -->
        </form>
    </body>
</html>