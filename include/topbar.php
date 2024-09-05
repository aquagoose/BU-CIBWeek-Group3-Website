<?php

require_once "checkauth.php";

?>

<link rel="stylesheet" type="text/css" href="styles/topbar.css">
<div id="topbar">
    <div id="topbar-left">
        <a class="tblink" href="./">Home</a>
        <?php if (hasRole("INCIDENT_MANAGER", "TECHNICAL_RESOLVER")) { ?>
            <a class="tblink" href="reports.php">Reports</a>
        <?php } ?>
        <a class="tblink" href="report.php">Report Incident</a>
        <?php if (hasRole("INCIDENT_MANAGER")) { ?>
            <a class="tblink" href="new.php">Create Incident</a>
        <?php } ?>
        <?php if (!hasRole("BUSINESS_USER")) { ?>
        <a class="tblink" href="tasks.php">Tasks</a>
        <?php } ?>
    </div>
    <div id="topbar-right">
        <span class="tbtext">Hi, <?php echo explode(' ', $_SESSION["name"])[0] ?>! 👋</span>
        <a class="tblink right" href="logout.php" style="margin-right: 0">Log out</a>
    </div>
</div>
