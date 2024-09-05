<?php
// Index - contains the incident reports table.
// Cannot be directed to if the user is not logged in, instead they are navigated to the login page.

require_once "include/checkauth.php";
require "include/database.php";

$db = Database::default();
$result = $db->execute("SELECT * FROM reports ORDER BY report_id DESC");

$table = "";
while ($row = $result->fetch_assoc()) {
    $reportId = $row["report_id"];
    $timeCreated = $row["time_created"];
    $contactName = $row["contact_name"];
    $contactEmail = $row["contact_email"];
    $contactNumber = $row["contact_number"];
    $desc = $row["description"];
    $impact = $row["impact"];

    $incidentResult = $db->execute("SELECT incident_id FROM incident_report_link WHERE report_id = ?", $reportId);
    $actions = "";
    if ($incident = $incidentResult->fetch_assoc()) {
        $actions .= "<a href='incident.php?id={$incident["incident_id"]}'>Incident</a>";
    }

    $table .= "<tr>
    <td>$reportId</td>
    <td>$timeCreated</td>
    <td>$contactName<br>$contactEmail<br>$contactNumber<br></td>
    <td>$impact</td>
    <td>$desc</td>
    <td>$actions</td>
</tr>";
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Incidents</title>
        <link rel="stylesheet" type="text/css" href="styles/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <?php include "include/topbar.php" ?>
        <div id="wrapper">
            <table id="incidents-table">
                <tr>
                    <th>ID</th>
                    <th>Time Reported</th>
                    <th>Contact</th>
                    <th>Impact</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                <?php echo $table ?>
            </table>
        </div>
    </body>
</html>