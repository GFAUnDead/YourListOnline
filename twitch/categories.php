<?php
// Initialize the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['access_token'])) {
    header("Location: login.php");
    exit;
}

// Require database connection
require_once "db_connect.php";

// Fetch the user's data from the database based on the access_token
$access_token = $_SESSION['access_token'];

$stmt = $conn->prepare("SELECT id, username FROM users WHERE access_token = ?");
$stmt->bind_param("s", $access_token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];
$username = $user['username'];

// Get categories from the database for the logged-in user
$query = "SELECT * FROM categories WHERE user_id = '$user_id' OR user_id IS NULL";
$result = $conn->query($query);

if (!$result) {
    die("Error retrieving categories: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Categories</title>
    <link rel="icon" href="https://cdn.yourlist.online/img/logo.png" type="image/png" />
    <link rel="apple-touch-icon" href="https://cdn.yourlist.online/img/logo.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.yourlist.online/css/list.css">
    <script src="https://cdn.yourlist.online/js/about.js"></script>
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }
        .wrapper {
            width: 350px;
            padding: 20px;
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
                    <li class="active"><a href="categories.php">View Categories</a></li>
                    <li><a href="add_category.php">Add Category</a></li>
                </ul>
            </li>
            <li class="dropdown dropdown-hover">
                <a class="dropdown" data-toggle="dropdown">Profile <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="profile.php">View Profile</a></li>
                    <li><a href="update_profile.php">Update Profile</a></li>
                    <li><a href="obs_options.php">OBS Viewing Options</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </li>
            <?php if ($_SESSION['is_admin']) { ?>
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
<h1>Welcome, <?php echo $username; ?>!</h1>
<h2>Here is the current list of categories you can filter your lists in, each category will be its own list.<br>
    Shown in this list are only the categories you have made. Using a category ID that you haven't created will result in a blank page.</h2>
<table class="table">
    <thead>
    <tr>
        <th style="width: 5%;">ID</th>
        <th>Category</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id']) ?></td>
            <td><?php echo htmlspecialchars($row['category']) ?></td>
        </tr>
    <?php endwhile ?>
    </tbody>
</table>
</body>
</html>