<?php
// Initialize the session
session_start();

// check if user is logged in
if (!isset($_SESSION['access_token'])) {
    header('Location: ../login.php');
    exit();
}

// Connect to database
require_once "../db_connect.php";

// Get the current hour in 24-hour format (0-23)
$currentHour = date('G');
// Initialize the greeting variable
$greeting = '';
// Check if it's before 12 PM (noon)
if ($currentHour < 12) {
    $greeting = "Good morning";
} else {
    $greeting = "Good afternoon";
}

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

// Fetch users for dropdown
$usersQuery = "SELECT id, username FROM users";
$usersResult = mysqli_query($conn, $usersQuery);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $selectedUser = $_POST['user'];
    $objective = $_POST['objective'];
    $category = $_POST['category'];

    // Prepare and execute query
    $stmt = $conn->prepare("INSERT INTO todos (user_id, objective, category, created_at, updated_at, completed) VALUES (?, ?, ?, NOW(), NOW(), 'No')");
    $stmt->bind_param("iss", $selectedUser, $objective, $category);
    $stmt->execute();
    header('Location: admin_dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>YourListOnline - Add Objective</title>
    <link rel="stylesheet" href="https://dhbhdrzi4tiry.cloudfront.net/cdn/sites/foundation.min.css">
    <link rel="stylesheet" href="https://cdn.yourlist.online/css/custom.css">
    <script src="https://cdn.yourlist.online/js/about.js"></script>
  	<link rel="icon" href="https://cdn.yourlist.online/img/logo.png" type="image/png" />
  	<link rel="apple-touch-icon" href="https://cdn.yourlist.online/img/logo.png">
  </head>
<body>
<!-- Navigation -->
<div class="title-bar" data-responsive-toggle="mobile-menu" data-hide-for="medium">
  <button class="menu-icon" type="button" data-toggle="mobile-menu"></button>
  <div class="title-bar-title">Menu</div>
</div>
<nav class="top-bar stacked-for-medium" id="mobile-menu">
  <div class="top-bar-left">
    <ul class="dropdown vertical medium-horizontal menu" data-dropdown-menu data-responsive-menu="drilldown medium-dropdown">
      <li class="menu-text menu-text-black">YourListOnline</li>
      <li><a href="../dashboard.php">User Dashboard</a></li>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li class="is-active"><a href="insert.php">Insert</a></li>
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
<form method="post" action="">
  <h3>Add Task for User</h3>
  <div class="medium-5 large-2 cell">
    <label for="user">Select User:</label>
    <select name="user" id="user" class="form-control">
        <option value="">PICK USER</option>
        <?php while ($row = mysqli_fetch_assoc($usersResult)) { ?>
            <option value="<?php echo $row['id']; ?>"><?php echo $row['username']; ?></option>
        <?php } ?>
    </select>
  </div>
  <div class="medium-5 large-6 cell">
      <label for="objective">Task Objective:</label>
      <textarea id="objective" name="objective" class="form-control"></textarea>
  </div>
  <div class="medium-5 large-2 cell">
      <label for="category">Category:</label>
      <select id="category" name="category" class="form-control">
          <?php
          // Retrieve categories from database
          $stmt = $conn->prepare("SELECT * FROM categories");
          $stmt->execute();
          $result = $stmt->get_result();
          
          // Display categories as options in dropdown menu
          while ($row = $result->fetch_assoc()) {
              echo '<option value="'.$row['id'].'">'.$row['category'].'</option>';
          }
          ?>
      </select>
  </div>
  <button type="submit" class="save-button" name="add_task">Add</button>
  <a href="dashboard.php">Cancel</a>
</form>
</div>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="https://dhbhdrzi4tiry.cloudfront.net/cdn/sites/foundation.js"></script>
<script src="https://cdn.yourlist.online/js/darkmode.js"></script>
<script>$(document).foundation();</script>
<script>
  // JavaScript function to handle the category filter change
  document.getElementById("categoryFilter").addEventListener("change", function() {
    var selectedCategoryId = this.value;
    // Redirect to the page with the selected category filter
    window.location.href = "insert.php?category=" + selectedCategoryId;
  });
</script>
</body>
</html>