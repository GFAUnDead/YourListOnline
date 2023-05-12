<?php
// Require database connection
require_once "db_connect.php";

// Initialize the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's data from the database
$user_id = $_SESSION['user_id'];

// Retrieve font and color data for the user from the showobs table
$stmt = $conn->prepare("SELECT * FROM showobs WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$settings = $result->fetch_assoc();

// Retrieve font and color data for the user from the showobs table
$font = isset($settings['font']) && $settings['font'] !== '' ? $settings['font'] : 'Not set';
$color = isset($settings['color']) && $settings['color'] !== '' ? $settings['color'] : 'Not set';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize the input
    $selectedFont = isset($_POST["font"]) ? $_POST["font"] : '';
    $selectedColor = isset($_POST["color"]) ? $_POST["color"] : '';

    // Check if the user has existing settings
    if ($result->num_rows > 0) {
        // Update the font and color data in the database
        $stmt = $conn->prepare("UPDATE showobs SET font = ?, color = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $selectedFont, $selectedColor, $user_id);
        if ($stmt->execute()) {
            // Update successful
            // echo "Settings updated successfully!";
            header("Location: " . $_SERVER['REQUEST_URI']);
        } else {
            // Display error message
            echo "Error updating settings: " . $stmt->error;
        }
    } else {
        // Insert new settings for the user
        $stmt = $conn->prepare("INSERT INTO showobs (user_id, font, color) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $selectedFont, $selectedColor);
        if ($stmt->execute()) {
            // Insertion successful
            // echo "Settings inserted successfully!";
            header("Location: " . $_SERVER['REQUEST_URI']);
        } else {
            // Display error message
            echo "Error inserting settings: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - OBS Viewing Options</title>
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
                    <li><a href="update_category.php">Update Category</a></li>
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
			      		<li><a href="profile.php">View Profile</a></li>
			      		<li><a href="update_profile.php">Update Profile</a></li>
                        <li class="active"><a href="obs_options.php">OBS Viewing Options</a></li>
                        <li><a href="logout.php">Logout</a></li>
			      	</ul>
            </li>
            <?php if ($_SESSION['is_admin']) { ?>
            <li class="dropdown dropdown-hover">
			      <a class="dropdown" data-toggle="dropdown">Admins <span class="caret"></span></a>
			      	<ul class="dropdown-menu">
                <li><a href="admin.php">Admin Dashboard</a></li>
			      	</ul>
            </li>
            <?php } ?>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
</nav>
<div class="col-md-6">
    <h1>Font and color Settings:</h1>
    <br><br>
    <form method="post">
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
        <div class="form-group">
            <label for="font">Font:</label>
            <select name="font" class="form-control">
                <option value="Arial">Arial</option>
                <option value="Verdana">Verdana</option>
                <option value="Times New Roman">Times New Roman</option>
                <!-- Add more font options here -->
            </select>
        </div>
        <div class="form-group">
            <label for="color">Color:</label>
            <select name="color" class="form-control">
                <option value="Black">Black</option>
                <option value="White">White</option>
                <option value="Red">Red</option>
                <option value="Blue">Blue</option>
                <!-- Add more color options here -->
            </select>
        </div>
        <input type="submit" value="Save" class="btn btn-primary">
    </form>
    <br><br>
    <h3>Your Current Settings:</h3>
    <?php if ($font !== '' || $color !== '') { ?>
        <p>Your selected font is: <?php echo $font ?></p>
        <p>Your selected color is: <?php echo $color ?></p>
    <?php } else { ?>
        <p>No font and color settings have been set.</p>
    <?php } ?>
</div>
</body>
</html>