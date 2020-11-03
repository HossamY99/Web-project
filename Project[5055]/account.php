<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <title>Project Mart</title>
    </head>

    <?php include "header.php";
        if(!$logged_in) {
            dieWithMessage("You are not logged in! Please sign up or log in first.");
        }
        $id = $_SESSION["user_id"];
        include "connect_db.php";
        $sql = $db->prepare("SELECT *
                            FROM user
                            WHERE id = :id;");
        $sql->bindParam(':id', $id);
        $sql->execute();
        $results = $sql->fetchAll(PDO::FETCH_ASSOC);

        /* TODO: Make these editable. Using ajax might be better than php, but either is fine.
        With PHP we would need a page to handle these updates, with ajax we can do it 
        immediately and asynchronously.
        Also add a way to change the password.*/
        echo "Your account information:
            <br>Your email: ". $results[0]["email"] .
            "<br>First name: ". $results[0]["first_name"] .
            "<br>Last name: ". $results[0]["last_name"];
        if(isset($results[0]["phone"]) && !empty($results[0]["phone"])) {
            echo "<br>Phone number: ". $results[0]["phone"];
        }

        // Shows a message and links then dies.
        function dieWithMessage($message) {
            echo $message;
            echo '<br><a href="login.php">Click here to log in</a>';
            echo '<br><a href="signup.php">Click here to sign up</a>';
            die();
        }
        ?>
    </body>
</html>