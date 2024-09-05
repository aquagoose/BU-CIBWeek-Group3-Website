<?php

require_once "include/checkauth.php";
require_once "include/database.php";
checkRole("INCIDENT_MANAGER", "TECHNICAL_RESOLVER");

$db = Database::default();

$result = null;
if (hasRole("INCIDENT_MANAGER")) {
    $result = $db->execute("SELECT assigned_user, incident_id, status, task_type, description, assigned_date, due_date, priority FROM tasks");
} else {
    $result = $db->execute("SELECT incident_id, status, task_type, description, assigned_date, due_date, priority FROM tasks WHERE assigned_user = ?", $_SESSION["username"]);
}

$table = "";
while ($row = $result->fetch_assoc()) {
    $incident = $row["incident_id"];
    $status = $row["status"];
    $taskType = $row["task_type"];
    $desc = $row["description"];
    $assignedDate = $row["assigned_date"];
    $dueDate = $row["due_date"];
    $priority = $row["priority"];

    $table .= "<tr>";
    if (hasRole("INCIDENT_MANAGER")) {
        $user = $row["assigned_user"];
        $table .= "<td>$user</td>";
    }
    $table .= "<td>$status</td>
    <td>$taskType</td>
    <td>$desc</td>
    <td>$assignedDate</td>
    <td>$dueDate</td>
    <td>$priority</td>
    <td><a href='incident.php?id=$incident'>Incident $incident</a></td>
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
            <?php if (hasRole("INCIDENT_MANAGER")) { ?>
                <th>Assigned User</th>
            <?php } ?>
            <th>Status</th>
            <th>Type</th>
            <th>Description</th>
            <th>Assigned Date</th>
            <th>Due Date</th>
            <th>Priority</th>
            <th>Actions</th>
        </tr>
        <?php echo $table ?>
    </table>
</div>
</body>
</html>