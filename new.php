<?php

require_once "include/checkauth.php";
require_once "include/database.php";

checkRole("INCIDENT_MANAGER");

if (isset($_POST["priority"])) {
    $db = Database::default();

    $priority = $_POST["priority"];
    $shortDesc = $_POST["summary"];
    $longDesc = $_POST["longdesc"];

    // No auto-increment, so we begin a transaction, get the newest incident ID, then adds it by 1 when inserting data.
    // Using transaction to ensure that it completes correctly before data is inserted, i.e. another incident is
    // submitted at the exact same time as this one, causing the IDs to be the same value.
    $db->beginTransaction();
    $db->executeNoResult("SELECT incident_id INTO @id FROM incidents ORDER BY incident_id DESC LIMIT 1");
    $db->executeNoResult("INSERT INTO incidents VALUES(@id + 1, ?, ?, ?, 'DETECTED', CURRENT_TIMESTAMP)", $priority, $shortDesc, $longDesc);
    $db->commit();

    header("location: ./incident.php");
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Create Incident</title>
        <link rel="stylesheet" type="text/css" href="styles/index.css">
        <link rel="stylesheet" type="text/css" href="styles/login.css">
    </head>
    <body>
        <?php include "include/topbar.php" ?>
        <div id="wrapper">
            <div id="form-content">
                <h1>Create Incident</h1>
                <form id="insert-form" method="POST">
                    <label for="priority">Priority</label>
                    <select name="priority">
                        <option value="HIGH">High</option>
                        <option value="MEDIUM">Medium</option>
                        <option value="LOW">Low</option>
                    </select>

                    <label for="summary">Summary</label>
                    <input name="summary" type="text" required>

                    <label for="longdesc">Description</label>
                    <textarea name="longdesc" required></textarea>
                    <input type="submit" value="Create">
                </form>
            </div>
        </div>
    </body>
</html>
