<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
  header("Location: login.php");
  exit();
}

// Require database connection
require_once "db_connect.php";

// Fetch the user's data from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);

// Check if the query succeeded
if (!$result) {
  echo "Error: " . mysqli_error($conn);
  exit();
}

// Get the user's data from the query result
$user_data = mysqli_fetch_assoc($result);

// Store the user's data in the $_SESSION variable
$_SESSION['user_data'] = $user_data;

// Get user's to-do list or all to-do list if the user is an admin
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === 1) {
	$sql = "SELECT * FROM todos ORDER BY id ASC";
  } else {
	$user_id = $_SESSION['user_id'];
	$sql = "SELECT * FROM todos WHERE user_id = '$user_id' ORDER BY id ASC";
  }

$result = mysqli_query($conn, $sql);

// Handle errors
if (!$result) {
  echo "Error: " . mysqli_error($conn);
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Dashboard</title>
  <link rel="icon" href="img/logo.png" type="image/png" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="css/style.css">
  <script src="js/about.js"></script>
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
            <li class="active"><a href="dashboard.php">Dashboard</a></li>
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
            <li><a href="profile.php">Profile</a></li>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
</nav>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <h2>Your Current List:</h2>
    <?php echo "Number of rows in your list: " . mysqli_num_rows($result); ?>    
    <table class="table">
      <thead>
        <tr>
          <th>Objective</th>
          <th>Category</th>
          <th>Created</th>
          <th>Last Updated</th>
          <th>Completed</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?php echo $row['objective']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td><?php echo $row['updated_at']; ?></td>
            <td><?php echo $row['completed']; ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  
</body>
</html>
