<?php ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); ?>
<?php
// Initialize the session
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
$_SESSION['is_admin'] = $user_data['is_admin'];

// Check if the user is an admin
if ($_SESSION['is_admin'] == 1) {
  // The user is an admin, load the page
  $sql = "SELECT todos.*, users.username FROM todos INNER JOIN users ON todos.user_id = users.id ORDER BY id ASC";
  $result = mysqli_query($conn, $sql);

  // Handle errors
  if (!$result) {
    echo "Error: " . mysqli_error($conn);
    exit();
  }
} else {
  // The user is not an admin, redirect to dashboard.php
  header("Location: dashboard.php");
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Dashboard</title>
  <link rel="icon" href="img/logo.png" type="image/png" />
  <link rel="apple-touch-icon" href="img/logo.png">
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
          <a class="navbar-brand" href="https://yourlist.online/">YourListOnline</a>
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
			      		<li><a href="update_profile.php">Update Profile</a></li>
			      	</ul>
            </li>
            <li class="active"><a href="admin.php">Admin Page</a></li>
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
          <th>Username</th>
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
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['objective']; ?></td>
            <td>
              <?php
                $category_id = $row['category'];
                $category_sql = "SELECT category FROM categories WHERE id = '$category_id'";
                $category_result = mysqli_query($conn, $category_sql);
                $category_row = mysqli_fetch_assoc($category_result);
                echo $category_row['category'];
              ?>
            </td>
            <td><?php echo $row['created_at']; ?></td>
            <td><?php echo $row['updated_at']; ?></td>
            <td><?php echo $row['completed']; ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
</body>
</html>