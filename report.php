<?php

require_once "include/checkauth.php";
require_once "include/database.php";

if (isset($_POST["name"])) {
    $db = Database::default();

    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["number"];
    $impact = $_POST["impact"];
    $desc = $_POST["description"];

    $db->beginTransaction();
    $db->executeNoResult("SELECT report_id INTO @id FROM reports ORDER BY report_id DESC LIMIT 1");
    $db->executeNoResult("INSERT INTO reports VALUES (@id + 1, CURRENT_TIMESTAMP, ?, ?, ?, ?, ?)", $name, $email, $phone, $desc, $impact);
    $db->commit();

    header("location: ./reports.php");
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Report Incident</title>
        <link rel="stylesheet" type="text/css" href="styles/index.css">
        <link rel="stylesheet" type="text/css" href="styles/login.css">
    </head>
    <body>
        <?php include "include/topbar.php" ?>
        <div id="wrapper">
            <div id="form-content">
                <h1>Report Incident</h1>
                <form id="insert-form" method="post">
                    <label for="name">Name</label>
                    <input type="text" name="name" required>

                    <label for="email">Contact Email</label>
                    <input type="email" name="email" required>

                    <label for="number">Contact Number</label>
                    <input type="tel" name="number" maxlength="11" required>

                    <label for="impact">Impact</label>
                    <input type="text" name="impact" required>

                    <label for="description">Description</label>
                    <textarea name="description" required></textarea>
                    <input type="submit" value="Submit">
                </form>
            </div>
        </div>
    </body>
</html>
