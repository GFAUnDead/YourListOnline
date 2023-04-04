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
$user_id = $_POST['user_id'];

// get form data
$title = $_POST['title'];
$description = $_POST['description'];

// prepare and execute query
$stmt = $conn->prepare("INSERT INTO todos (user_id, title, description) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $title, $description);
$stmt->execute();
?>

<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Add New Items</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
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
                <li class="active"><a href="insert.php">Add</a></li>
                <li><a href="completed.php">Completed</a></li>
                <li><a href="update.php">Update</a></li>
                <li><a href="remove.php">Remove</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
            <p class="navbar-text navbar-right">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</p>
        </div>
    </nav>
  <h1>Add New Todo Item</h1>
  <form method="post">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required><br><br>
    <label for="description">Description:</label>
    <textarea id="description" name="description"></textarea><br><br>
    <input type="hidden" name="user_id" value="<?php echo $_SESSION["user_id"]; ?>">
    <input type="submit" value="Add">
  </form>
</body>
</html>