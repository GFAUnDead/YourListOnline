<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to dashboard page
if(isset($_SESSION["twitchloggedin"]) && $_SESSION["twitchloggedin"] === true){
    header("location: dashboard.php");
    exit;
}

// Require database connection
require_once "db_connect.php";

?>