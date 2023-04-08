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
$sql = "SELECT * FROM todos WHERE user_id = $user_id ORDER BY created_at DESC";
$result = $conn->query($sql);

if ($result) {
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    echo "Error: " . mysqli_error($conn);
}

// Update tasks if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($tasks as $task) {
        $task_id = $task['id'];
        $new_task = $_POST[$task_id];

        // Check if the task has been updated
        if ($new_task != $task['task']) {
            $sql = "UPDATE tasks SET task = '$new_task' WHERE id = " . intval($task_id);
            mysqli_query($conn, $sql);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Update</title>
    <link rel="icon" href="img/logo.png" type="image/png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
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
            <a class="navbar-brand" href="index.php">YourListOnline</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="insert.php">Add</a></li>
            <li><a href="completed.php">Completed</a></li>
            <li class="active"><a href="update.php">Update</a></li>
            <li><a href="remove.php">Remove</a></li>
            <li><a href="change_password.php">Password Change</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
  </nav>
    
    <h1>My To-Do List</h1>
    <form method="POST">
        <table class="table">
            <thead>
                <tr>
                    <th>Existing</th>
                    <th>Update</th>
                    <th>Update Task</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $task) { ?>
                <tr>
                    <td><?php echo $task['task']; ?></td>
                    <td><input type="text" name="<?php echo $task['id']; ?>" class="form-control"></td>
                    <td><button type="submit" class="btn btn-primary">Update</button></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </form>
</body>
</html>