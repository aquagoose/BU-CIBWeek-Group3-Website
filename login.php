<?php
// If user is already logged in - redirect to home.
session_start();
if (isset($_SESSION["username"]))
    header("Location: ./");

require "include/database.php";

$db = Database::default();

$errorMsg = "";

if (isset($_POST["username"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $result = $db->execute("SELECT username, name, role FROM users WHERE username = ? AND password = PASSWORD(?)", $username, $password);
    if ($row = $result->fetch_assoc()) {
        session_start();
        $_SESSION["username"] = $row["username"];
        $_SESSION["name"] = $row["name"];
        $_SESSION["role"] = $row["role"];

        if (isset($_GET["redirect"])) {
            header("Location: {$_GET["redirect"]}");
        } else {
            header("Location: ./");
        }
    } else {
        $errorMsg = "Incorrect username or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Log In</title>
        <link rel="stylesheet" type="text/css" href="styles/index.css">
        <link rel="stylesheet" type="text/css" href="styles/login.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div id="wrapper">
            <div id="form-content">
                <h1>DWK Incident Response System</h1>
                <p>You must log in to access this system.</p>
                <form id="login-form" method="POST" autocomplete="off">
                    <input id="login-user" name="username" type="text" placeholder="Username" required /><br />
                    <input id="login-pass" name="password" type="password" placeholder="Password" required /><br />
                    <input id="login-submit" type="submit" value="Log In" /><br />
                    <span style="color: red; font-size: 20px"><?php echo "$errorMsg" ?></span>
                </form>
            </div>
        </div>
    </body>
</html>
