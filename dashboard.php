<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Require database connection
require_once "db_connect.php";

// Get user's to-do list
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM todos WHERE user_id = $user_id ORDER BY created_at DESC";
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
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">YourListOnline</a>
            </div>
            <ul class="nav navbar-nav">
                <li class="active"><a href="dashboard.php">Dashboard</a></li>
                <li><a href="completed.php">Completed</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <h2>Your to-do list:</h2>
    <ul>
     <?php while ($row = mysqli_fetch_assoc($result)): ?>
          <li>
            <h3><?php echo $row['title']; ?></h3>
            <p><?php echo $row['description']; ?></p>
            <p>Created at: <?php echo $row['created_at']; ?></p>
            <p>Updated at: <?php echo $row['updated_at']; ?></p>
            <p>Completed: <?php echo $row['completed'] ? 'Yes' : 'No'; ?></p>
          </li>
     <?php endwhile; ?>
     </ul>
  
</body>
</html>
