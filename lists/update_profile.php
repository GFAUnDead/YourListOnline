<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit();
}

// Require database connection
require_once "db_connect.php";

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    // Get user's Twitch profile image URL
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
    $twitch_profile_image_url = $response;

    // Prepare an update statement
    $sql = "UPDATE users SET profile_image = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("si", $twitch_profile_image_url, $user_id);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            // Redirect to profile page
            header("location: profile.php");
            exit();
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Update Profile</title>
  <link rel="icon" href="img/logo.png" type="image/png" />
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
            <li class="active"><a href="profile.php">Profile</a></li>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
</nav>
<div class="col-md-6">
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <h2>Update Profile<br></h2>
    <p>Click the button below to set your profile image.</p>
    <button class="btn btn-primary" id="update-profile-image-button">Update Profile Image</button>
    <script>
    document.getElementById('update-profile-image-button').addEventListener('click', function() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_profile_image.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                // Update the profile image on the page
                var img = document.getElementById('profile-image');
                img.src = xhr.responseText;
            } else {
                console.log('Error: ' + xhr.status);
            }
        };
        xhr.send();
    });
    </script>
</div>
</body>
</html>