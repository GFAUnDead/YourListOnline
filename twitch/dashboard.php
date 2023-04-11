<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['twitchloggedin'])) {
    header("Location: logincall.php");
    exit;
}

// Require database connection
require_once "db_connect.php";

?>