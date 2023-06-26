<?php
// Initialize the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['access_token'])) {
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
  $category = $_POST['category'];

  // prepare and execute query
  $stmt = $conn->prepare("INSERT INTO todos (user_id, objective, category, created_at, updated_at, completed) VALUES (?, ?, ?, NOW(), NOW(), 'No')");
  $stmt->bind_param("iss", $user_id, $objective, $category);
  $stmt->execute();
  header('Location: dashboard.php');
  exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Add New Items</title>
  <link rel="icon" href="https://cdn.yourlist.online/img/logo.png" type="image/png" />
  <link rel="apple-touch-icon" href="https://cdn.yourlist.online/img/logo.png">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="https://cdn.yourlist.online/css/insert.css">
  <script src="https://cdn.yourlist.online/js/about.js"></script>
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
            <li class="active"><a href="insert.php">Add</a></li>
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
  <div class="col-md-6">
  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
      <form method="post">
      <h3>Please enter your task to add it to your list:</h3>
        <div class="form-group">
          <textarea id="objective" name="objective" class="form-control"></textarea>
        </div>
        <div class="form-group">
          <label for="category">Category:</label>
          <select id="category" name="category" class="form-control">
            <?php
            // retrieve categories from database
            $stmt = $conn->prepare("SELECT * FROM categories WHERE user_id = '$user_id' OR user_id IS NULL");
            $stmt->execute();
            $result = $stmt->get_result();
      
            // display categories as options in dropdown menu
            while ($row = $result->fetch_assoc()) {
              echo '<option value="'.$row['id'].'">'.$row['category'].'</option>';
            }
            ?>
          </select>
        </div>
        <input type="hidden" name="user_id" value="<?php echo $_SESSION["user_id"]; ?>">
        <button type="submit" class="btn btn-primary">Add</button>
        <a href="dashboard.php" class="btn btn-default">Cancel</a>
      </form>
    </div>
</body>
</html>