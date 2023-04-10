<?php
// Initialize the session
session_start();

// Check if the user is already logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
} else {
    // Require database connection
    require_once "db_connect.php";

    // Get user information from the database
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT username, signup_date, last_login, api_key FROM users WHERE id = ?";
    if($stmt = $conn->prepare($sql)){
        $stmt->bind_param("i", $user_id);
        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows == 1){
                $stmt->bind_result($username, $signup_date, $last_login, $api_key);
                $stmt->fetch();
                $_SESSION['signup_date'] = $signup_date;
                $_SESSION['last_login'] = $last_login;
                $_SESSION['api_key'] = $api_key;
            } else {
                echo "Oops! Something went wrong. Please try again later.";
                exit;
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Profile</title>
  <link rel="icon" href="img/logo.png" type="image/png" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
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
            <li class="active"><a href="profile.php">Profile</a></li>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
  </nav>
  <h1>Your Profile</h1>
<div class="wrapper">
  <p><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
  <p><strong>Joined:</strong> <?php echo date('F j, Y', strtotime($_SESSION['signup_date'])); ?> (AET)</p>
  <p><strong>Last Login:</strong> <?php echo date('F j, Y', strtotime($_SESSION['last_login'])); ?> at <?php echo date('g:i A', strtotime($last_login)); ?> (AET)</p>
  <p><strong>API Key:</strong> <span class="api-key-wrapper" style="display: none;"><?php echo $api_key; ?></span></p>
  <button type="button" class="btn btn-primary" id="show-api-key">Show API Key</button>
  <button type="button" class="btn btn-primary" id="hide-api-key" style="display:none;">Hide API Key</button>
  <br><br>
  <button class="btn btn-primary" onclick="showOBSInfo()">HOW TO PUT ON STREAM</button>
  <br><br>
  <!--<a href="change_password.php" class="btn btn-primary">Change Password</a>-->
  <a href="logout.php" class="btn btn-primary">Logout</a>
</div>
</body>
</html>