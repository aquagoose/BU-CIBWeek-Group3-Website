<?php

require_once "include/checkauth.php";
require_once "include/database.php";

checkRole("INCIDENT_MANAGER");

$db = Database::default();

if (isset($_POST["priority"])) {
    $id = $_GET["id"];
    $priority = $_POST["priority"];
    $status = $_POST["status"];
    $summary = $_POST["summary"];
    $desc = $_POST["longdesc"];

    $db->executeNoResult("UPDATE incidents SET priority = ?, status = ?, short_description = ?, long_description = ? WHERE incident_id = ?", $priority, $status, $summary, $desc, $id);

    header("location: incident.php?id={$_GET["id"]}");
}

$result = $db->execute("SELECT * FROM incidents WHERE incident_id = ?", $_GET["id"]);

if (!$row = $result->fetch_assoc()) {
    die("Failed to get incident with ID {$_GET["id"]}");
}

$id = $row["incident_id"];
$priority = $row["priority"];
$status = $row["status"];
$summary = $row["short_description"];
$desc = $row["long_description"];

function echoSelected(string $expected, string $actual): void {
    if ($expected == $actual) {
        echo "selected";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Edit Incident <?php echo $id ?></title>
        <link rel="stylesheet" type="text/css" href="styles/index.css">
        <link rel="stylesheet" type="text/css" href="styles/login.css">
    </head>
    <body>
    <?php include "include/topbar.php" ?>
    <div id="wrapper">
        <div id="form-content">
            <h1>Edit Incident <?php echo $id ?></h1>
            <form id="insert-form" method="POST">
                <label for="priority">Priority</label>
                <select name="priority">
                    <option value="HIGH" <?php echoSelected("HIGH", $priority); ?>>High</option>
                    <option value="MEDIUM" <?php echoSelected("MEDIUM", $priority); ?>>Medium</option>
                    <option value="LOW" <?php echoSelected("LOW", $priority); ?>>Low</option>
                </select>

                <label for="status">Status</label>
                <select name="status">
                    <option value="DETECTED" <?php echoSelected("DETECTED", $status); ?>>Detected</option>
                    <option value="IN_PROGRESS" <?php echoSelected("IN_PROGRESS", $status); ?>>In Progress</option>
                    <option value="MITIGATED" <?php echoSelected("MITIGATED", $status); ?>>Mitigated</option>
                    <option value="RESOLVED" <?php echoSelected("RESOLVED", $status); ?>>Resolved</option>
                </select>

                <label for="summary">Summary</label>
                <input name="summary" type="text" value="<?php echo $summary ?>" required>

                <label for="longdesc">Description</label>
                <textarea name="longdesc" required><?php echo $desc ?></textarea>
                <input type="submit" value="Done">
            </form>
        </div>
    </div>
    </body>
</html>
