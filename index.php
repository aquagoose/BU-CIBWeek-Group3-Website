<?php
// Index - contains the incident reports table.
// Cannot be directed to if the user is not logged in, instead they are navigated to the login page.

require_once "include/checkauth.php";

require "include/database.php";

$orderBy = "incident_id";
$sort = "DESC";

if (isset($_GET["order"])) {
    $sort = "ASC";
    switch ($_GET["order"]) {
        case "status":
            // Since we are not using an Enum in the database, and using a VARCHAR2 for our status instead of a number
            // (for better or for worse), we need to "convert" each possible status into a number to allow sorting to
            // work correctly. Otherwise SQL just sorts the status alphabetically.
            $orderBy = "
            CASE status
                WHEN 'DETECTED' THEN 0
                WHEN 'IN_PROGRESS' THEN 1
                WHEN 'MITIGATED' THEN 2
                WHEN 'RESOLVED' THEN 3
            END";
            break;

        case "priority":
            // Ditto as above.
            $orderBy = "
            CASE priority
                WHEN 'HIGH' THEN 0
                WHEN 'MEDIUM' THEN 1
                WHEN 'LOW' THEN 2 
            END";
            break;
    }
}

// May look redundant, but it's to prevent SQL injection.
// Since we have to insert the value directly into the query instead of using prepared statements, we need to sanitize
// the output to prevent injection.
if (isset($_GET["sort"])) {
    switch ($_GET["sort"]) {
        case "asc":
            $sort = "ASC";
            break;

        case "desc":
            $sort = "DESC";
            break;
    }
}

$db = Database::default();
$result = $db->execute("SELECT * FROM incidents ORDER BY $orderBy $sort");

$table = "";
$jsArray = [];
while ($row = $result->fetch_assoc()) {
    $incidentId = $row["incident_id"];
    $timeDetected = $row["time_detected"];
    $shortDesc = $row["short_description"];

    // "Prettify" the output.
    // I could do this programmatically, converting to lowercase then changing the first character to uppercase...
    // But it's a pain in the backside, plus we could expand to have inputs such as "VERY_HIGH" which just adds complexity.
    $priority = "UNKNOWN";
    $priorityAttribs = "";
    switch ($row["priority"]) {
        case "LOW":
            $priority = "Low";
            break;

        case "MEDIUM":
            $priority = "Medium";
            break;

        case "HIGH":
            $priority = "High";
            $priorityAttribs = "class='high-priority'";
            break;
    }

    $status = "UNKNOWN";
    $statusAttribs = "";
    switch ($row["status"])
    {
        case "DETECTED":
            $status = "Detected";
            $statusAttribs = "class='high-priority'";
            break;

        case "IN_PROGRESS":
            $status = "In progress";
            break;

        case "MITIGATED":
            $status = "Mitigated";
            break;

        case "RESOLVED":
            $status = "Resolved";
            break;
    }

    $table .= "<tr>
    <td>$incidentId</td>
    <td>$timeDetected</td>
    <td $statusAttribs>$status</td>
    <td $priorityAttribs>$priority</td>
    <td>$shortDesc</td>
    <td><a href='javascript:showDetails($incidentId)'>Details</a><br /><a href='incident.php?id=$incidentId'>View</a></td>
</tr>";

    $jsArray[$incidentId] = "<span><b>ID:</b> $incidentId</span><br />
    <span><b>Status:</b> $status</span><br />
    <span><b>Detected:</b> $timeDetected</span><br />
    <span><b>Last update:</b> $timeDetected</span><br />
    <span><b>Description:</b> ${row["long_description"]}</span>";
}

function generateUrl(string $order, string $sort): string {
    switch ($sort) {
        case "ASC":
            $sort = "desc";
            break;

        case "DESC":
            $sort = "asc";
            break;
    }

    return http_build_query(array_merge($_GET, array("order" => $order, "sort" => $sort)));
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Incidents</title>
        <link rel="stylesheet" type="text/css" href="styles/index.css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script type="text/javascript">
            const incidents = <?php echo json_encode($jsArray) ?>;

            function showDetails(incidentId) {
                document.getElementById("details-modal").style.display = "block";
                document.getElementById("details-message").innerHTML = incidents[incidentId];
            }

            function closeDetails() {
                document.getElementById("details-modal").style.display = "none";
            }
        </script>
    </head>
    <body>
        <?php include "include/topbar.php" ?>

        <div id="details-modal">
            <div id="details-content">
                <div id="details-message"></div>
                <div id="details-footer">
                    <button onclick="closeDetails()">Close</button>
                </div>
            </div>
        </div>
        <div id="wrapper">
            <table id="incidents-table">
                <tr>
                    <th><a href="?<?php echo generateUrl("id", $sort) ?>">ID</a></th>
                    <th>Time Submitted</th>
                    <th><a href="?<?php echo generateUrl("status", $sort) ?>">Status</a></th>
                    <th><a href="?<?php echo generateUrl("priority", $sort) ?>">Priority</a></th>
                    <th>Summary</th>
                    <th>Actions</th>
                </tr>
                <?php echo $table ?>
            </table>
        </div>
    </body>
</html>