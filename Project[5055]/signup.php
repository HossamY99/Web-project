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
        <form action="signup_handler.php" method="post">
            <label for="email"><span class="asterisk">*</span>Email:</label><br>
            <input type="text" name="email"><br>
            <label for="password"><span class="asterisk">*</span>Password:</label><br>
            <input type="password" name="password"><br>
            <label for="password2"><span class="asterisk">*</span>Confirm password:</label><br>
            <input type="password" name="password2"><br>

            <label for="first_name"><span class="asterisk">*</span>First name:</label><br>
            <input type="text" name="first_name"><br>
            <label for="last_name"><span class="asterisk">*</span>Last name:</label><br>
            <input type="text" name="last_name"><br>
            <label for="phone">Phone:</label><br>
            <input type="text" name="phone"><br><br>

            <input type="submit" name="signup" value="Sign Up"><br>

            <?php
            if(isset($_GET["signup"]) && $_GET["exists"] == "1") {
                echo "Username already exists!";
            }
            ?>
        </form>
    </body>
</html>