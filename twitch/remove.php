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
$is_admin = ($user['admin'] == 1);

// Get the selected category filter, default to "all" if not provided
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';

// Build the SQL query based on the category filter
$user_id = $_SESSION['user_id'];
if ($categoryFilter === 'all') {
  $sql = "SELECT * FROM todos WHERE user_id = '$user_id' ORDER BY id ASC";
} else {
  $categoryFilter = mysqli_real_escape_string($conn, $categoryFilter);
  $sql = "SELECT * FROM todos WHERE user_id = '$user_id' AND category = '$categoryFilter' ORDER BY id ASC";
}

$result = mysqli_query($conn, $sql);

// Handle errors
if (!$result) {
  echo "Error: " . mysqli_error($conn);
  exit();
}

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
    
    <!-- Category filter dropdown -->
    <div class="category-filter">
      <label for="categoryFilter">Filter by Category:</label>
      <select id="categoryFilter" onchange="applyCategoryFilter()">
        <option value="all" <?php if ($categoryFilter === 'all') echo 'selected'; ?>>All</option>
        <?php
          $categories_sql = "SELECT * FROM categories WHERE user_id = '$user_id' OR user_id IS NULL";
          $categories_result = mysqli_query($conn, $categories_sql);

          while ($category_row = mysqli_fetch_assoc($categories_result)) {
            $categoryId = $category_row['id'];
            $categoryName = $category_row['category'];
            $selected = ($categoryFilter == $categoryId) ? 'selected' : '';
            echo "<option value=\"$categoryId\" $selected>$categoryName</option>";
          }
        ?>
      </select>
    </div>

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

<script>
  // JavaScript function to handle the category filter change
  document.getElementById("categoryFilter").addEventListener("change", function() {
    var selectedCategoryId = this.value;
    // Redirect to the page with the selected category filter
    window.location.href = "remove.php?category=" + selectedCategoryId;
  });
</script>
</body>
</html>