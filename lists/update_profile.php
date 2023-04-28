<?php ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); ?>
<?php
session_start();

// check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

// Require database connection
require_once "db_connect.php";

// Get user's Twitch profile image URL
$username = $_SESSION['username'];
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
$_SESSION['twitch_profile_image_url'] = $response;

// Set default value for the Twitch profile image URL
if (!isset($twitch_profile_image_url)) {
    $twitch_profile_image_url = '';
}

// Check if form has been submitted
if (isset($_POST['update_profile'])) {
    // Get new username from form data
    $new_username = $_POST['new_username'];

    // Update user's username in database
    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->bind_param("si", $new_username, $_SESSION['user_id']);
    $stmt->execute();

    // Set new username in session
    $_SESSION['username'] = $new_username;

    // Check if profile image should also be updated
    if (isset($_POST['update_profile_image'])) {
        // Update user's profile image URL in database
        $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->bind_param("si", $twitch_profile_image_url, $_SESSION['user_id']);
        $stmt->execute();

        // Set new profile image URL in session
        $_SESSION['profile_image'] = $twitch_profile_image_url;
        
        // Redirect to profile page
        header("Location: profile.php");
        exit();
    }

    // Redirect to logout page to allow user to login with new username
    header("Location: logout.php");
    exit();
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Update Profile</title>
  <link rel="icon" href="img/logo.png" type="image/png" />
  <link rel="apple-touch-icon" href="img/logo.png">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/about.js"></script>
  <script src="js/obsbutton.js"></script>
  <script src="js/profile.js"></script>
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
            <a class="navbar-brand" href="../index.php">YourListOnline</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="insert.php">Add</a></li>
            <li><a href="completed.php">Completed</a></li>
            <li><a href="update.php">Update</a></li>
            <li><a href="remove.php">Remove</a></li>
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
			      		<li><a href="profile.php">View Profile</a></li>
			      		<li class="active"><a href="update_profile.php">Update Profile</a></li>
			      	</ul>
            </li>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
</nav>
<div class="col-md-6">
    <h1>Updating profile for: <?php echo $_SESSION['username']; ?></h1>
    <form action="update_profile.php" method="POST">
        <h2>Update Username</h2>
        <div>
          <label for="twitch_username">Twitch Username:</label>
          <input type="text" id="twitch_username" name="twitch_username" value="<?php echo $_SESSION['username']; ?>">
        </div>
        <div>
          <button class="btn btn-primary" type="submit" name="update_username">Update Username</button>
        </div>
    </form>
    <form id="update-profile-image-form" action="update_profile.php" method="POST">
        <h2>Update Profile Image</h2>
        <div>
        <img id="profile-image" src="<?php echo $_SESSION['twitch_profile_image_url']; ?>" weidth="100px" height="100px" alt="New Profile Image">
        </div>
        <div>
            <input type="hidden" name="twitch_profile_image_url" id="twitch_profile_image_url" value="">
            <button class="btn btn-primary" id="update-profile-image-button">Update New Profile Image</button>
        </div>
    </form>
</div>
</body>
</html>