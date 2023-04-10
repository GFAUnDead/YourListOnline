<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

// Require database connection
require_once "db_connect.php";

?>
<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Catergories</title>
    <link rel="icon" href="img/logo.png" type="image/png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
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
            <li><a href="completed.php">Completed</a></li>
            <li><a href="update.php">Update</a></li>
            <li><a href="remove.php">Remove</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
  </nav>
  <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
  <h1>Here is the current list of categories you can filter your lists in, each categories will be it's own list.</h1>
  <table class="table">
      <thead>
          <tr>
              <th>Category</th>
          </tr>
      </thead>
      <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
              <td><?php echo htmlspecialchars($row['category']) ?></td>
          </tr>
          <?php endwhile ?>
      </tbody>
  </table>
</body>
</html>