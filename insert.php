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
  $description = $_POST['description'];

  // prepare and execute query
  $stmt = $conn->prepare("INSERT INTO todos (user_id, description, created_at, updated_at, completed) VALUES (?, ?, NOW(), NOW(), 0)");
  $stmt->bind_param("is", $user_id, $description);
  $stmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Add New Items</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <link rel="stylesheet" href="css/insert.css">
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
        <li><a href="change_password.php">Password Change</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
      <p class="navbar-text navbar-right">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</p>
    </div>
  </nav>

  <div class="container">
    <h1>Add New Todo Item</h1>
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <form method="post">
          <div class="form-group">
            <label for="description">Task:</label>
            <textarea id="description" name="description" class="form-control"></textarea>
          </div>
          <input type="hidden" name="user_id" value="<?php echo $_SESSION["user_id"]; ?>">
          <button type="submit" class="btn btn-primary">Add</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>