<?php
// Initialize the session
session_start();

// check if user is logged in
if (!isset($_SESSION['access_token'])) {
    header('Location: login.php');
    exit();
}

// Require database connection
require_once "db_connect.php";

// Default Timezone Settings
$defaultTimeZone = 'Etc/UTC';
$user_timezone = $defaultTimeZone;

// Fetch the user's data from the database based on the access_token
$access_token = $_SESSION['access_token'];
$stmt = $conn->prepare("SELECT * FROM users WHERE access_token = ?");
$stmt->bind_param("s", $access_token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];
$username = $user['username'];
$twitchDisplayName = $user['twitch_display_name'];
$twitch_profile_image_url = $user['profile_image'];
$is_admin = ($user['is_admin'] == 1);
$user_timezone = $user['timezone'];
date_default_timezone_set($user_timezone);

// Determine the greeting based on the user's local time
$currentHour = date('G');
$greeting = '';

if ($currentHour < 12) {
    $greeting = "Good morning";
} else {
    $greeting = "Good afternoon";
}

// Get the selected category filter, default to "all" if not provided
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : 'all';

// Build the SQL query based on the category filter
if ($categoryFilter === 'all') {
  $sql = "SELECT * FROM todos WHERE user_id = '$user_id' ORDER BY id ASC";
} else {
  $categoryFilter = mysqli_real_escape_string($conn, $categoryFilter);
  $sql = "SELECT * FROM todos WHERE user_id = '$user_id' AND category = '$categoryFilter' ORDER BY id ASC";
}

$result = mysqli_query($conn, $sql);
$num_rows = mysqli_num_rows($result);

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
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>YourListOnline - Remove Objective</title>
    <link rel="stylesheet" href="https://dhbhdrzi4tiry.cloudfront.net/cdn/sites/foundation.min.css">
    <link rel="stylesheet" href="https://cdn.yourlistonline.com.au/css/custom.css">
    <script src="https://cdn.yourlistonline.com.au/js/about.js"></script>
    <script src="https://cdn.yourlistonline.com.au/js/sorttable.js"></script>
  	<link rel="icon" href="https://cdn.yourlistonline.com.au/img/logo.png" type="image/png" />
  	<link rel="apple-touch-icon" href="https://cdn.yourlistonline.com.au/img/logo.png">
  </head>
<body>
<!-- Navigation -->
<div class="title-bar" data-responsive-toggle="mobile-menu" data-hide-for="medium">
  <button class="menu-icon" type="button" data-toggle="mobile-menu"></button>
  <div class="title-bar-title">Menu</div>
</div>
<nav class="top-bar stacked-for-medium" id="mobile-menu">
  <div class="top-bar-left">
    <ul class="dropdown vertical medium-horizontal menu" data-responsive-menu="drilldown medium-dropdown hinge-in-from-top hinge-out-from-top">
      <li class="menu-text menu-text-black">YourListOnline</li>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="insert.php">Add</a></li>
      <li class="is-active"><a href="remove.php">Remove</a></li>
      <li>
        <a>Update</a>
        <ul class="vertical menu" data-dropdown-menu>
          <li><a href="update_objective.php">Update Objective</a></li>
          <li><a href="update_category.php">Update Objective Category</a></li>
        </ul>
      </li>
      <li><a href="completed.php">Completed</a></li>
      <li>
        <a>Categories</a>
        <ul class="vertical menu" data-dropdown-menu>
          <li><a href="categories.php">View Categories</a></li>
          <li><a href="add_category.php">Add Category</a></li>
        </ul>
      </li>
      <li>
        <a>Profile</a>
        <ul class="vertical menu" data-dropdown-menu>
          <li><a href="profile.php">View Profile</a></li>
          <li><a href="update_profile.php">Update Profile</a></li>
          <li><a href="obs_options.php">OBS Viewing Options</a></li>
          <li><a href="twitch_mods.php">Twitch Mods</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>
      </li>
      <?php if ($is_admin) { ?>
        <li>
        <a>Admins</a>
        <ul class="vertical menu" data-dropdown-menu>
					<li><a href="../admins/dashboard.php" target="_self">Admin Dashboard</a></li>
        </ul>
      </li>
      <?php } ?>
    </ul>
  </div>
  <div class="top-bar-right">
    <ul class="menu">
      <li><button id="dark-mode-toggle"><i class="icon-toggle-dark-mode"></i></button></li>
      <li><a class="popup-link" onclick="showPopup()">&copy; 2023 YourListOnline. All rights reserved.</a></li>
    </ul>
  </div>
</nav>
<!-- /Navigation -->

<div class="row column">
<br>
<h1><?php echo "$greeting, <img id='profile-image' src='$twitch_profile_image_url' width='50px' height='50px' alt='$twitchDisplayName Profile Image'>$twitchDisplayName!"; ?></h1>
<br>
<?php if ($num_rows < 1) {} else { ?>
<!-- Category Filter Dropdown & Search Bar-->
<div class="search-and-filter">
  <form method="GET" action="">
    <input type="text" name="search" placeholder="Search todos" class="search-input">
  </form>
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
<!-- /Category Filter Dropdown & Search Bar -->
<?php } ?>

<div class="row column">
<?php if ($num_rows < 1) { echo '<h3 style="color: red;">There are no rows to edit</h3>'; } else { ?>
<h1>Please pick which task to remove from your list:</h1>
<table class="sortable dark-mode-table">
    <thead>
        <tr>
            <th width="500">Objective</th>
            <th width="300">Category</th>
            <th width="200">Completed</th>
            <th width="200">Remove</th>
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
                        <button type="submit" class="save-button">Remove</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php } ?>
</div>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://dhbhdrzi4tiry.cloudfront.net/cdn/sites/foundation.js"></script>
<script src="https://cdn.yourlistonline.com.au/js/darkmode.js"></script>
<script>$(document).foundation();</script>
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