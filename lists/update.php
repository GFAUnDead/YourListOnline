<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
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
    $tasks = $result->fetch_all(MYSQLI_ASSOC);
} else {
    echo "Error: " . mysqli_error($conn);
}

// Update tasks if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($tasks as $task) {
        $task_id = $task['id'];
        $new_objective = $_POST[$task_id]['objective'];
        $new_category = $_POST[$task_id]['category'];

        // Check if the task has been updated
        if ($new_objective != $task['objective'] || $new_category != $task['category']) {
            $sql = "UPDATE todos SET objective = '$new_objective', category = '$new_category' WHERE id = " . intval($task_id);
            mysqli_query($conn, $sql);
        }
    }
    header('Location: update.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Update</title>
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
            <a class="navbar-brand" href="../index.php">YourListOnline</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="insert.php">Add</a></li>
            <li><a href="completed.php">Completed</a></li>
            <li class="active"><a href="update.php">Update</a></li>
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
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
</nav>
            <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
            <h1>Please pick which task to update on your list:</h1>
            <table class="table">
                <thead>
                  <tr>
                    <th>Objective</th>
                    <th>Category</th>
                    <th>Update</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($tasks as $task) { ?>
                    <tr>
                      <td><?php echo $task['objective']; ?></td>
                      <td><?php echo $row['category']; ?></td>
                      <td>
                        <input type="text" name="objective[<?php echo $task['id']; ?>]" class="form-control" value="<?php echo $task['objective']; ?>">
                      </td>
                      <td>
                        <select class="form-control" name="category[<?php echo $task['id']; ?>]">
                          <?php foreach ($categories as $category) { ?>
                            <option value="<?php echo $category['name']; ?>" <?php if ($category['name'] == $task['category']) { echo 'selected'; } ?>><?php echo $category['name']; ?></option>
                          <?php } ?>
                        </select>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
            </table>
</body>
</html>