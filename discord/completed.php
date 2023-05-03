<?php
// Initialize the session
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

$sql = "SELECT * FROM todos WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Mark task as completed
if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE todos SET completed = 'Yes' WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    header('Location: completed.php');
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Completed</title>
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
            <li class="active"><a href="completed.php">Completed</a></li>
            <li><a href="update.php">Update</a></li>
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
  <h1>Please pick which task to mark as completed:</h1>
  <table class="table">
      <thead>
          <tr>
              <th>Objective</th>
              <th>Category</th>
              <th>Completed</th>
              <th>Action</th>
          </tr>
      </thead>
      <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
              <td><?php echo htmlspecialchars($row['objective']) ?></td>
              <td>
              <?php
                $category_id = $row['category'];
                $category_sql = "SELECT category FROM categories WHERE id = '$category_id'";
                $category_result = mysqli_query($conn, $category_sql);
                $category_row = mysqli_fetch_assoc($category_result);
                echo $category_row['category'];
              ?>
            </td>
              <td><?php echo $row['completed']; ?></td>
              <td>
                <form method="post" action="completed.php">
                    <input type="hidden" name="task_id" value="<?php echo $row['id'] ?>">
                    <button type="submit">Mark as Completed</button>
                </form>
              </td>
          </tr>
          <?php endwhile ?>
      </tbody>
  </table>
</body>
</html>