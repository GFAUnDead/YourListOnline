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

// Get user's incomplete tasks
$user_id = $_SESSION['user_id'];

// Check if a specific category is selected
if (isset($_GET['category'])) {
    $category_id = $_GET['category'];
    $sql = "SELECT * FROM todos WHERE user_id = ? AND category = ? AND completed = 'No'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $category_id);
} else {
    $sql = "SELECT * FROM todos WHERE user_id = ? AND completed = 'No'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Assign incomplete tasks to the $incompleteTasks variable
$incompleteTasks = [];
while ($row = $result->fetch_assoc()) {
    $incompleteTasks[] = $row;
}

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Set is_admin session variable
$_SESSION['is_admin'] = $user_data['is_admin'];

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

// Retrieve categories for the filter dropdown
$categorySql = "SELECT * FROM categories";
$categoryResult = mysqli_query($conn, $categorySql);
$categories = mysqli_fetch_all($categoryResult, MYSQLI_ASSOC);

// Check if a specific category is selected
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';
?>
<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Completed</title>
    <link rel="icon" href="https://cdn.yourlist.online/img/logo.png" type="image/png" />
    <link rel="apple-touch-icon" href="https://cdn.yourlist.online/img/logo.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.yourlist.online/css/list.css">
    <script src="https://cdn.yourlist.online/js/about.js"></script>
    <style type="text/css">
        body {
            font: 14px sans-serif;
        }
        .wrapper {
            width: 350px;
            padding: 20px;
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
            <li class="active"><a href="completed.php">Completed</a></li>
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
            <?php if ($_SESSION['is_admin']) { ?>
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
<h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>

<!-- Category filter dropdown -->
<div class="category-filter">
  <label for="categoryFilter">Filter by Category:</label>
  <select id="categoryFilter" onchange="applyCategoryFilter()">
    <option value="all" <?php if ($categoryFilter === 'all') echo 'selected'; ?>>All</option>
    <?php foreach ($categories as $category): ?>
      <?php $categoryId = $category['id']; ?>
      <?php $categoryName = $category['category']; ?>
      <?php $selected = ($categoryFilter == $categoryId) ? 'selected' : ''; ?>
      <option value="<?php echo $categoryId; ?>" <?php echo $selected; ?>><?php echo $categoryName; ?></option>
    <?php endforeach; ?>
  </select>
</div>

<h1>Completed Tasks:</h1>
<p>Number of total tasks in the category: <?php echo count($incompleteTasks); ?></p>
<table class="table">
    <thead>
    <tr>
        <th>Objective</th>
        <th>Category</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($incompleteTasks as $row): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['objective']); ?></td>
            <?php
            $category_id = $row['category'];
            $category_sql = "SELECT category FROM categories WHERE id = '$category_id'";
            $category_result = mysqli_query($conn, $category_sql);
            $category_row = mysqli_fetch_assoc($category_result);
            echo $category_row['category'];
            ?>
            <td>
                <form method="post" action="completed.php">
                    <input type="hidden" name="task_id" value="<?php echo $row['id']; ?>">
                    <button type="submit">Mark as Completed</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
  // JavaScript function to handle the category filter change
  document.getElementById("categoryFilter").addEventListener("change", function() {
    var selectedCategoryId = this.value;
    // Redirect to the page with the selected category filter
    window.location.href = "completed.php?category=" + selectedCategoryId;
  });
</script>
</body>
</html>