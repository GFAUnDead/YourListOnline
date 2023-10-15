<?php ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); ?>
<?php
// Initialize the session
session_start();

// check if user is logged in
if (!isset($_SESSION['access_token'])) {
    header('Location: login.php');
    exit();
}

// Require database connection
require_once "db_connect.php";

// Default Timezone Settings
$defaultTimeZone = 'Etc/UTC';
$user_timezone = $defaultTimeZone;

// Fetch the user's data from the database based on the access_token
$access_token = $_SESSION['access_token'];

$stmt = $conn->prepare("SELECT * FROM users WHERE access_token = ?");
$stmt->bind_param("s", $access_token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];
$username = $user['username'];
$twitchDisplayName = $user['twitch_display_name'];
$is_admin = ($user['is_admin'] == 1);
$user_timezone = $user['timezone'];
date_default_timezone_set($user_timezone);

// Determine the greeting based on the user's local time
$currentHour = date('G');
$greeting = '';

if ($currentHour < 12) {
    $greeting = "Good morning";
} else {
    $greeting = "Good afternoon";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedTimeZone = $_POST["timezone"];

    // Update the user's time zone in the database
    $stmt = $conn->prepare("UPDATE users SET timezone = ? WHERE access_token = ?");
    $stmt->bind_param("si", $selectedTimeZone, $access_token);
    $stmt->execute();

    // Update the user's time zone in the current session
    $user_timezone = $selectedTimeZone;
}

// Prepare to get all the timezones
$timezone_prepare = $conn->prepare("SELECT * FROM timezones");
if (!$timezone_prepare) {
    die("Error in preparing the statement: " . $conn->error);
}

// Execute the prepared statement
if ($timezone_prepare->execute()) {
    // Bind the result to variables matching the columns
    $timezone_prepare->bind_result($timezone_id, $timezone_name);

    // Fetch the timezones into an array
    $timezones = [];

    while ($timezone_prepare->fetch()) {
        $timezones[] = [
            'id' => $timezone_id,
            'name' => $timezone_name,
        ];
    }

    // Close the statement
    $timezone_prepare->close();
} else {
    die("Error in executing the statement: " . $timezone_prepare->error);
}

// Construct the URL for the Twitch profile image
$url = 'https://decapi.me/twitch/avatar/' . $username;

// Initialize cURL session
$curl = curl_init();
// Set cURL options
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => $url,
));
// Execute cURL request and get response
$response = curl_exec($curl);
// Close cURL session
curl_close($curl);
// Set Twitch profile image URL to the response
$twitch_profile_image_url = trim($response);

// Check if form has been submitted to update the profile image
if (isset($_POST['update_profile_image'])) {
    // Get new profile image URL from form data
    $twitch_profile_image_url = $_POST['twitch_profile_image_url'];

    // Update user's profile image URL in database
    $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE access_token = ?");
    $stmt->bind_param("ss", $twitch_profile_image_url, $access_token);
    $stmt->execute();
    // Redirect to profile page
    header("Location: profile.php");
    exit();
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>YourListOnline - Update Profile</title>
    <link rel="stylesheet" href="https://dhbhdrzi4tiry.cloudfront.net/cdn/sites/foundation.min.css">
    <link rel="stylesheet" href="https://cdn.yourlist.online/css/custom.css">
    <script src="https://cdn.yourlist.online/js/about.js"></script>
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
      <li class="menu-text menu-text-black">YourListOnline</li>
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
          <li><a href="profile.php">View Profile</a></li>
          <li><a href="update_profile.php">Update Profile</a></li>
          <li><a href="obs_options.php">OBS Viewing Options</a></li>
          <li><a href="twitch_mods.php">Twitch Mods</a></li>
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
      <li><button id="dark-mode-toggle"><i class="icon-toggle-dark-mode"></i></button></li>
      <li><a class="popup-link" onclick="showPopup()">&copy; 2023 YourListOnline. All rights reserved.</a></li>
    </ul>
  </div>
</nav>
<!-- /Navigation -->

<div class="row column">
<br>
<h1><?php echo "$greeting, $username!"; ?></h1>
<br>
<table>
  <tr>
    <th>Update Profile Image</th>
    <th>Choose your time zone</th>
  </tr>
  <tbody>
  <tr>
    <td>
      <form id="update-profile-image-form" action="update_profile.php" method="POST">
        <div><img id="profile-image" src="<?php echo $twitch_profile_image_url; ?>" width="100px" height="100px" alt="<?php echo $username; ?> New Profile Image"></div>
        <div>
          <input type="hidden" name="twitch_profile_image_url" value="<?php echo $twitch_profile_image_url; ?>">
          <button class="save-button" id="update-profile-image-button" name="update_profile_image">Update New Profile Image</button>
        </div>
      </form>
    </td>
    <td>
      <form action="" method="post">
        <select name="timezone"><?php foreach ($timezones as $timezone) { $selected = ($timezone['name'] == $user_timezone) ? 'selected' : ''; echo "<option value='{$timezone['name']}'$selected>{$timezone['name']}</option>"; } ?></select>
        <input type="submit" value="Submit" class="save-button">
      </form>
    </td>
  </tr>
  </tbody>
</table>
</div>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://dhbhdrzi4tiry.cloudfront.net/cdn/sites/foundation.js"></script>
<script src="https://cdn.yourlist.online/js/darkmode.js"></script>
<script>$(document).foundation();</script>
</body>
</html>