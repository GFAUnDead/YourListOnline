<?php ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); ?>
<?php
// Initialize the session
session_start();

// Check if the user is already logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
} 

// Require database connection
require_once "db_connect.php";

// Get the current hour in 24-hour format (0-23)
$currentHour = date('G');
// Initialize the greeting variable
$greeting = '';
// Check if it's before 12 PM (noon)
if ($currentHour < 12) {
    $greeting = "Good morning";
} else {
    $greeting = "Good afternoon";
}

// Get user information from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user_data = mysqli_fetch_assoc($result);
$is_admin = $user_data['is_admin'];
$username = $user_data['username'];
$signup_date = $user_data['signup_date'];
$last_login = $user_data['last_login'];
$api_key = $user_data['api_key'];
$change_password = $user_data['change_password'];
$twitch_profile_image_url = $user_data['profile_image'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>YourListOnline - Profile</title>
    <link rel="stylesheet" href="https://dhbhdrzi4tiry.cloudfront.net/cdn/sites/foundation.min.css">
    <link rel="stylesheet" href="https://cdn.yourlist.online/css/custom.css">
    <link rel="icon" href="https://cdn.yourlist.online/img/logo.png" type="image/png" />
    <link rel="apple-touch-icon" href="https://cdn.yourlist.online/img/logo.png">
</head>
<body>
<!-- Navigation -->
<div class="title-bar" data-responsive-toggle="mobile-menu" data-hide-for="medium">
    <button class="menu-icon" type="button" data-toggle="mobile-menu"></button>
    <div class="title-bar-title">Menu</div>
</div>
<nav class="top-bar stacked-for-medium" id="mobile-menu">
    <div class="top-bar-left">
        <ul class="dropdown vertical medium-horizontal menu" data-responsive-menu="drilldown medium-dropdown hinge-in-from-top hinge-out-from-top">
            <li class="menu-text">YourListOnline</li>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="insert.php">Add</a></li>
            <li><a href="remove.php">Remove</a></li>
            <li>
                <a>Update</a>
                <ul class="vertical menu" data-dropdown-menu>
                    <li><a href="update_objective.php">Update Objective</a></li>
                    <li><a href="update_category.php">Update Objective Category</a></li>
                </ul>
            </li>
            <li><a href="completed.php">Completed</a></li>
            <li>
                <a>Categories</a>
                <ul class="vertical menu" data-dropdown-menu>
                    <li><a href="categories.php">View Categories</a></li>
                    <li><a href="add_category.php">Add Category</a></li>
                </ul>
            </li>
            <li>
                <a>Profile</a>
                <ul class="vertical menu" data-dropdown-menu>
                    <li class="is-active"><a href="profile.php">View Profile</a></li>
                    <li><a href="update_profile.php">Update Profile</a></li>
                    <li><a href="obs_options.php">OBS Viewing Options</a></li>
                    <?php if ($change_password) { ?> <li><a href="change_password.php">Change Password</a></li>  <?php } ?>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </li>
            <?php if ($is_admin) { ?>
            <li>
                <a>Admins</a>
                <ul class="vertical menu" data-dropdown-menu>
                    <li><a href="../admins/dashboard.php" target="_self">Admin Dashboard</a></li>
                </ul>
            </li>
            <?php } ?>
        </ul>
    </div>
    <div class="top-bar-right">
        <ul class="menu">
            <li><a class="popup-link" onclick="showPopup()">&copy; 2023 YourListOnline. All rights reserved.</a></li>
        </ul>
    </div>
</nav>
<!-- /Navigation -->
<div class="row column">
    <br>
    <h1><?php echo "<h1>$greeting, $username!</h1>"; ?></h1>
    <h2>Your Profile</h2>
    <img src="<?php echo $twitch_profile_image_url; ?>" width="150px" height="150px" alt="Twitch Profile Image for <?php echo $username; ?>">
    <br><br>
    <p><strong>Your Username:</strong> <?php echo $username; ?></p>
    <p><strong>You Joined:</strong> <?php echo date('F j, Y', strtotime($signup_date)); ?> (AET)</p>
    <p><strong>Your Last Login:</strong> <?php echo date('F j, Y', strtotime($last_login)); ?> at <?php echo date('g:i A', strtotime($last_login)); ?> (AET)</p>
    <p><strong>Your API Key:</strong> <span class="api-key-wrapper" style="display: none;"><?php echo $api_key; ?></span></p>
    <button type="button" class="defult-button" id="show-api-key">Show API Key</button>
    <button type="button" class="defult-button" id="hide-api-key" style="display:none;">Hide API Key</button>
    <br><br>
    <button class="defult-button" onclick="showOBSInfo()">HOW TO PUT ON YOUR STREAM</button>
    <br><br>
    <?php if ($change_password) { ?><a href="change_password.php" class="defult-button">Change Password</a><br><br><?php } ?>
    <a href="logout.php" class="logout-button">Logout</a>
</div>
<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://cdn.yourlist.online/js/profile.js"></script>
<script src="https://cdn.yourlist.online/js/about.js" defer></script>
<script src="https://cdn.yourlist.online/js/obsbutton.js" defer></script>
<script src="https://dhbhdrzi4tiry.cloudfront.net/cdn/sites/foundation.js"></script>
<script>$(document).foundation();</script>
</body>
</html>