<?php
// Initialize the session
session_start();

// check if user is logged in
if (!isset($_SESSION['access_token'])) {
    header('Location: login.php');
    exit();
}

// Connect to database
require_once "db_connect.php";

// Fetch the user's data from the database based on the access_token
$access_token = $_SESSION['access_token'];
$stmt = $conn->prepare("SELECT * FROM users WHERE access_token = ?");
$stmt->bind_param("s", $access_token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];
$username = $user['username'];
$signup_date = $user['signup_date'];
$last_login = $user['last_login'];
$api_key = $user['api_key'];
$twitch_profile_image_url = $user['profile_image'];
$is_admin = ($user['is_admin'] == 1);
?>
<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Profile</title>
  <link rel="icon" href="https://cdn.yourlist.online/img/logo.png" type="image/png" />
  <link rel="apple-touch-icon" href="https://cdn.yourlist.online/img/logo.png">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.yourlist.online/css/list.css">
  <script src="https://cdn.yourlist.online/js/about.js"></script>
  <script src="https://cdn.yourlist.online/js/obsbutton.js"></script>
  <script src="https://cdn.yourlist.online/js/profile.js"></script>
  <style type="text/css">
    body {
      font: 14px sans-serif;
    }
    .wrapper {
      width: 350px; padding: 20px;
    }
    a.popup-link {
      text-decoration: none;
      color: black;
      cursor: pointer;
    }
  </style>
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="https://yourlist.online/">YourListOnline</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="insert.php">Add</a></li>
            <li><a href="remove.php">Remove</a></li>
            <li class="dropdown dropdown-hover">
                <a class="dropdown" data-toggle="dropdown">Update <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="update_objective.php">Update Objective</a></li>
                    <li><a href="update_category.php">Update Objective Category</a></li>
                </ul>
            </li>
            <li><a href="completed.php">Completed</a></li>
            <li class="dropdown dropdown-hover">
                <a class="dropdown" data-toggle="dropdown">Categories <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="categories.php">View Categories</a></li>
                    <li><a href="add_category.php">Add Category</a></li>
                </ul>
            </li>
            <li class="dropdown dropdown-hover">
			      <a class="dropdown" data-toggle="dropdown">Profile <span class="caret"></span></a>
			      	<ul class="dropdown-menu">
			      		<li class="active"><a href="profile.php">View Profile</a></li>
			      		<li><a href="update_profile.php">Update Profile</a></li>
                        <li><a href="obs_options.php">OBS Viewing Options</a></li>
                        <li><a href="logout.php">Logout</a></li>
			      	</ul>
            </li>
            <?php if ($is_admin) { ?>
            <li class="dropdown dropdown-hover">
			      <a class="dropdown" data-toggle="dropdown">Admins <span class="caret"></span></a>
			      	<ul class="dropdown-menu">
                <li><a href="admins/dashboard.php">Admin Dashboard</a></li>
			      	</ul>
            </li>
            <?php } ?>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
</nav>
<div class="col-md-6">
    <h1>Your Profile</h1>
    <img src="<?php echo $twitch_profile_image_url; ?>" width="150px" height="150px" alt="Twitch Profile Image for <?php echo $_SESSION['username']; ?>">
    <br><br>
    <p><strong>Username:</strong> <?php echo $username; ?></p>
    <p><strong>Joined:</strong> <?php echo date('F j, Y', strtotime($_SESSION['signup_date'])); ?> (AET)</p>
    <p><strong>Last Login:</strong> <?php echo date('F j, Y', strtotime($_SESSION['last_login'])); ?> at <?php echo date('g:i A', strtotime($last_login)); ?> (AET)</p>
    <p><strong>API Key:</strong> <span class="api-key-wrapper" style="display: none;"><?php echo $api_key; ?></span></p>
    <button type="button" class="btn btn-primary" id="show-api-key">Show API Key</button>
    <button type="button" class="btn btn-primary" id="hide-api-key" style="display:none;">Hide API Key</button>
    <br><br>
    <button class="btn btn-primary" onclick="showOBSInfo()">HOW TO PUT ON YOUR STREAM</button>
    <br><br>
    <a href="logout.php" class="btn btn-primary">Logout</a>
</div>
</body>
</html>