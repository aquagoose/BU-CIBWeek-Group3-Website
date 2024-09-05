<?php

session_start();
if (empty($_SESSION["username"]))
    header("Location: ./login.php?redirect={$_SERVER['REQUEST_URI']}");

function checkRole(string ...$roles): void {
    if (!in_array($_SESSION["role"], $roles)) {
        header("refresh: 5; url=./");
        die("Invalid permissions! Redirecting to homepage in 5 seconds...");
    }
}

function hasRole(string ...$roles): bool {
    return in_array($_SESSION["role"], $roles);
}

?>

<!--
    BU CIB 2024
    Code by Ollie Robinson, written in HTML + CSS + PHP
    Implementing the design done by everyone else :)
-->
