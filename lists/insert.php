<?php
session_start();

// check if user is logged in
if (!isset($_SESSION['loggedin'])) {
  header('Location: login.php');
  exit();
}

// connect to database
require_once 'db_connect.php';

// get user ID from session
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // get form data
  $objective = $_POST['objective'];

  // prepare and execute query
  $stmt = $conn->prepare("INSERT INTO todos (user_id, objective, created_at, updated_at, completed) VALUES (?, ?, NOW(), NOW(), 'No')");
  $stmt->bind_param("is", $user_id, $objective);
  $stmt->execute();
  header('Location: dashboard.php');
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Add New Items</title>
  <link rel="icon" href="img/logo.png" type="image/png" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="css/insert.css">
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
            <li><a href="dashboard.php">Dashboard</a></li>
            <li class="active"><a href="insert.php">Add</a></li>
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
  <div class="col-md-6">
  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
  <h1>Please enter your task to add it to your list:</h1>
      <form method="post">
        <div class="form-group">
          <textarea id="objective" name="objective" class="form-control"></textarea>
        </div>
        <input type="hidden" name="user_id" value="<?php echo $_SESSION["user_id"]; ?>">
        <button type="submit" class="btn btn-primary">Add</button>
      </form>
    </div>
</body>
</html>