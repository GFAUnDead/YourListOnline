<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
  header("Location: login.php");
  exit();
}

// Require database connection
require_once "db_connect.php";

// Get user's to-do list
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM todos WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

// Handle errors
if (!$result) {
  echo "Error: " . mysqli_error($conn);
  exit();
}

// Display user's to-do list
?>

<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Dashboard</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  <link rel="stylesheet" href="style.css">
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
            <li class="active"><a href="dashboard.php">Dashboard</a></li>
            <li><a href="insert.php">Add</a></li>
            <li><a href="completed.php">Completed</a></li>
            <li><a href="update.php">Update</a></li>
            <li><a href="remove.php">Remove</a></li>
            <li><a href="change_password.php">Password Change</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <p class="navbar-text navbar-right">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</p>
    </div>
  </nav>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <h2>Your to-do list:</h2>
    <table>
      <thead>
        <tr>
          <th>Title</th>
          <th>Description</th>
          <th>Created At</th>
          <th>Updated At</th>
          <th>Completed</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td><?php echo $row['updated_at']; ?></td>
            <td><?php echo $row['completed'] ? 'Yes' : 'No'; ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  
</body>
</html>
