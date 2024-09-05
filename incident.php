<?php

require_once "include/checkauth.php";
require_once "include/database.php";

if (empty($_GET["id"])) {
    header("location: ./");
    return;
}

$db = Database::default();
$result = $db->execute("SELECT * FROM incidents WHERE incident_id = ?", $_GET["id"]);

$arr = $result->fetch_assoc();
if (!$arr) {
    die("Failed to get incident with ID {$_GET["id"]}");
}

$id = $arr["incident_id"];
$priority = $arr["priority"];
$summary = $arr["short_description"];
$desc = $arr["long_description"];
$status = $arr["status"];
$timeDetected = $arr["time_detected"];

$result->close();
$result = $db->execute("SELECT time_stamp, contents, current_incident_status, business_impact FROM logs WHERE incident_id = ? ORDER BY log_id DESC", $id);

$tableHtml = "";
while ($row = $result->fetch_assoc()) {
    $time = $row["time_stamp"];
    $content = $row["contents"];
    $currentStatus = $row["current_incident_status"];
    $impact = $row["business_impact"];

    $tableHtml .= "<tr>
    <td>$time</td>
    <td>$content</td>
    <td>$impact</td>
    <td>$currentStatus</td>
</tr>";
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>View Incident <?php echo $id ?></title>
        <link rel="stylesheet" type="text/css" href="styles/index.css">
        <link rel="stylesheet" type="text/css" href="styles/incident.css">
    </head>
    <body>
        <?php include "include/topbar.php" ?>
        <div id="wrapper">
            <h2>Viewing Incident <?php echo $id ?></h2>
            <a href="">Download reports</a>
            <?php if (hasRole("INCIDENT_MANAGER")) { ?>
                <a href="edit.php?id=<?php echo $id ?>" style="margin-left: 20px">Edit</a>
            <?php } ?>
            <div id="details-grid">
                <div id="details-column">
                    <span><b>ID: </b><?php echo $id ?></span><br>
                    <span><b>Detected: </b><?php echo $timeDetected ?></span><br>
                    <span><b>Status: </b><?php echo $status ?></span><br>
                    <span><b>Priority: </b><?php echo $priority ?></span><br>
                    <span><b>Summary: </b><?php echo $summary ?></span><br><br>
                    <span><b>Description</b></span><br>
                    <span><?php echo $desc ?><span>
                </div>
                <div id="logs-column">
                    <h3>Logs</h3>
                    <table>
                        <tr>
                            <th>Time</th>
                            <th>Content</th>
                            <th>Impact</th>
                            <th>Current Status</th>
                        </tr>
                        <?php echo $tableHtml ?>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>