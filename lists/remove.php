<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Connect to database
require_once "db_connect.php";

// Get user's to-do list
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM todos WHERE user_id = $user_id ORDER BY completed DESC";
$result = $conn->query($sql);

// Handle remove item form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $todo_id = $_POST['todo_id'];

    // Delete item from database
    $sql = "DELETE FROM todos WHERE id = $todo_id";
    $result = $conn->query($sql);

    // Redirect back to remove page
    header('Location: remove.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Remove Item</title>
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
            <li><a href="completed.php">Completed</a></li>
            <li><a href="update.php">Update</a></li>
            <li class="active"><a href="remove.php">Remove</a></li>
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
                        <li><a href="logout.php">Logout</a></li>
			      	</ul>
            </li>
            <?php if ($_SESSION['is_admin']) { ?>
              <li><a href="admin.php">Admin Page</a></li>
            <?php } ?>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
</nav>
    <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
    <h1>Please pick which task to remove from your list:</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Objective</th>
                <th>Category</th>
                <th>Completed</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['objective'] ?></td>
                    <td>
                      <?php
                        $category_id = $row['category'];
                        $category_sql = "SELECT category FROM categories WHERE id = '$category_id'";
                        $category_result = mysqli_query($conn, $category_sql);
                        $category_row = mysqli_fetch_assoc($category_result);
                        echo $category_row['category'];
                      ?>
                    </td>
                    <td><?= $row['completed'] ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="todo_id" value="<?= $row['id'] ?>">
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
