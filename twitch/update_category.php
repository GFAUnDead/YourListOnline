<?php
// Initialize the session
session_start();

// check if user is logged in
if (!isset($_SESSION['access_token'])) {
    header('Location: login.php');
    exit();
}

// Connect to database
require_once "db_connect.php";

// Fetch the user's data from the database based on the access_token
$access_token = $_SESSION['access_token'];

$stmt = $conn->prepare("SELECT * FROM users WHERE access_token = ?");
$stmt->bind_param("s", $access_token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];
$username = $user['username'];
$is_admin = ($user['is_admin'] == 1);

// Get user's to-do list
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
      $new_category = $_POST['category'][$row_id];

      // Check if the row has been updated
      if ($new_category != $row['category']) {
          $sql = "UPDATE todos SET category = '$new_category' WHERE id = " . intval($row_id);
          mysqli_query($conn, $sql);
      }
  }
  header('Location: update_category.php');
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Update Objective Category</title>
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
            <?php if ($is_admin) { ?>
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
<h1>Please pick which row to update on your list:</h1>
<table class="table">
    <thead>
        <tr>
            <th>Objective</th>
            <th>Category</th>
            <th>Update Category</th>
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
                    <select id="category" name="category[<?php echo $row['id']; ?>]" class="form-control">
                        <?php
                            // retrieve categories from database
                            $stmt = $conn->prepare("SELECT * FROM categories WHERE user_id = '$user_id' OR user_id IS NULL");
                            $stmt->execute();
                            $result = $stmt->get_result();

                            // display categories as options in dropdown menu
                            while ($category_row = $result->fetch_assoc()) {
                                $selected = ($category_row['id'] == $row['category']) ? 'selected' : '';
                                echo '<option value="'.$category_row['id'].'" '.$selected.'>'.$category_row['category'].'</option>';
                            }
                        ?>
                    </select>
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
                echo '<h3 style="color: red;">There are no rows to edit</h3>';
            }
        ?>
        </tr>
        </form>
    </tbody>
</table>
</body>
</html>