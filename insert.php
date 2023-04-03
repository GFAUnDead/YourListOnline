<?php
session_start();

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

// connect to database
require_once 'db_connect.php';

// get user ID from session
$user_id = $_SESSION['user_id'];

// get form data
$title = $_POST['title'];
$description = $_POST['description'];

// prepare and execute query
$stmt = $conn->prepare("INSERT INTO todos (user_id, title, description) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_id, $title, $description);
$stmt->execute();

// redirect back to dashboard
header('Location: dashboard.php');
exit();
?>

<!DOCTYPE html>
<html>
<head>
  <title>YourListOnline - Add New Items</title>
</head>
<body>
  <h1>Add New Todo Item</h1>
  <form method="post">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required><br><br>
    <label for="description">Description:</label>
    <textarea id="description" name="description"></textarea><br><br>
    <input type="hidden" name="user_id" value="<?php echo $_SESSION["user_id"]; ?>">
    <input type="submit" value="Add">
  </form>
</body>
</html>