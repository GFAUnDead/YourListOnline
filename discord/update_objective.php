<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['access_token'])) {
    header("Location: login.php");
    exit;
}

// Require database connection
require_once "db_connect.php";

// Get user's to-do list
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM todos WHERE user_id = $user_id ORDER BY id DESC";
$result = $conn->query($sql);

if ($result) {
  $rows = $result->fetch_all(MYSQLI_ASSOC);
} else {
  error_log("Error: " . mysqli_error($conn));
  header("Location: error.php");
  exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  foreach ($rows as $row) {
      $row_id = $row['id'];
      $new_objective = $_POST['objective'][$row_id];

      // Check if the objective has been updated
      if ($new_objective != $row['objective']) {
          $sql = "UPDATE todos SET objective = '$new_objective' WHERE id = " . intval($row_id);
          mysqli_query($conn, $sql);
      }
  }
  header('Location: update_objective.php');
  exit;
}
?> 
<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Update Objective</title>
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
                        <li><a href="obs_options.php">OBS Viewing Options</a></li>
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
<h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
<h1>Please pick which row to update on your list:</h1>
<table class="table">
    <thead>
        <tr>
            <th>Objective</th>
            <th>Category</th>
            <th>Update Objective</th>
        </tr>
    </thead>
    <tbody>
        <form method="POST">
        <?php foreach ($rows as $row) { ?>
            <tr>
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
                <td>
                    <input type="text" name="objective[<?php echo $row['id']; ?>]" class="form-control" value="<?php echo $row['objective']; ?>">
                </td>
            </tr>
        <?php } ?>
        <tr>
        <?php
            // Check if the query succeeded
            if (!$result) {
              echo "Error: " . mysqli_error($conn);
              exit();
            }

            // Get the number of rows in the result
            $num_rows = mysqli_num_rows($result);

            // Check if there are any rows to edit
            if ($num_rows > 0) {
              echo '<td colspan="3"><button type="submit" name="submit" class="btn btn-primary">Update All</button></td>';
            } else {
              echo 'There are no rows to edit';
            }
        ?>
        </tr>
        </form>
    </tbody>
</table>
</body>
</html>