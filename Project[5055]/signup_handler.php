<?php
include "connect_db.php";

checkParams();
$email = $_POST["email"];
$pwd = $_POST["password"];
$fname = $_POST["first_name"];
$lname = $_POST["last_name"];
$phone = $_POST["phone"];
if(!$phone) { // If phone was not entered, set it to null in the database
    $phone = null;
}

if(!isUserExists($email)) { // User does not exist, create the new user
    $hash = md5(rand(0,1000)); // Hash for email verification

    $sql = $db->prepare("INSERT INTO user (email, pwd, first_name, last_name, phone, verification_hash)
                        VALUES (:email, :pwd, :fname, :lname, :phone, :verification_hash);");
    $sql->bindParam(':email', $email);
    $sql->bindParam(':pwd', md5($pwd));
    $sql->bindParam(':fname', $fname);
    $sql->bindParam(':lname', $lname);
    $sql->bindParam(':phone', $phone);
    $sql->bindParam(':verification_hash', $hash);
    try {
        $sql->execute();
        // Automatically log in the new user
        session_start();
        $_SESSION["user_id"] = $db->lastInsertId();
        $_SESSION["first_name"] = $fname;

        sendVerificationEmail($email, $hash);

        header("Location: index.php");
        die();
    } catch(PDOException $error) { // Error inserting the user
        dieWithMessage($sql ."<br>". $e->getMessage());
    }
}

/* Checks that the parameters were entered properly, and validates them.
   Kills the script if they are not valid. */
function checkParams() {
    /* TODO: This should validate the input with regex.
       Should also first be validated with javascript client-side.
       Ideally these should never be invalid server-side since they will
       be validated with javascript first. Included just in case of a malicious user */

    // Regex for email: "\^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$\"
    if(!isset($_POST["email"]) || empty($_POST["email"])) {
        dieWithMessage("Please enter a valid email.");
    }
    if(!isset($_POST["password"]) || empty($_POST["password"])) {
        dieWithMessage("Please enter a valid password.");
    }
    if(!isset($_POST["password2"]) || empty($_POST["password2"])
       || $_POST["password"] != $_POST["password2"]) {
        dieWithMessage("Password confirmation invalid.");
    }
    if(!isset($_POST["first_name"]) || empty($_POST["first_name"])) {
        dieWithMessage("Please enter a valid first name");
    }
    if(!isset($_POST["last_name"]) || empty($_POST["last_name"])) {
        dieWithMessage("Please enter a valid last name");
    }
    if(!isset($_POST["last_name"]) || empty($_POST["last_name"])) {
        dieWithMessage("Please enter a valid last name.");
    }
}

// Returns true if the user already exists in the database
function isUserExists($email) {
    include "connect_db.php";
    $sql = $db->prepare("SELECT id
                         FROM user
                         WHERE email = :email");
    $sql->bindParam(':email', $email);
    $sql->execute();
    $results = $sql->fetchAll(PDO::FETCH_ASSOC);

    // TODO: Does if($results) work here?
    if(count($results) > 0) { // User exists
        header("Location: signup.php?exists=1");
        die();
    }
    return false;
}

// Shows a message and links then dies.
function dieWithMessage($message) {
    echo $message;
    echo '<br><a href="signup.php">Click here to go back</a>';
    die();
}

// Sends a verification email. This would work with a mail server installed.
function sendVerificationEmail($email, $hash) {
    $to = $email;
    $subject = 'Blue Gear Signup | Verification';
    $message = 'Thank you for signing up to Blue Gear.

    Your account has been created, please click the following link to activate your account:

    http://localhost/cmps278/project/verify.php?email='. $email .'&hash='. $hash;
                         
    $headers = 'From:noreply@bluegear.aub.edu' . "\r\n"; // Set from headers
    mail($to, $subject, $message, $headers); // Sends the email
}
?>