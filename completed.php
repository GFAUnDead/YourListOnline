<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

require_once "db_connect.php";

// Mark task as completed
if (isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE todos SET completed = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Get user's to-do list
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM todos WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Completed</title>
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
            <a class="navbar-brand" href="index.php">YourListOnline</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="insert.php">Add</a></li>
            <li class="active"><a href="completed.php">Completed</a></li>
            <li><a href="update.php">Update</a></li>
            <li><a href="remove.php">Remove</a></li>
            <li><a href="change_password.php">Password Change</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
  </nav>
    
    <h1>My To-Do List</h1>
    <table>
        <thead>
            <tr>
                <th>Task ID</th>
                <th>Objective</th>
                <th>Created</th>
                <th>Last Updated</th>
                <th>Completed</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']) ?></td>
                <td><?php echo htmlspecialchars($row['description']) ?></td>
                <td><?php echo htmlspecialchars($row['created_at']) ?></td>
                <td><?php echo htmlspecialchars($row['updated_at']) ?></td>
                <td><?php echo $row['completed'] ? 'Yes' : 'No' ?></td>
                <td>
                    <?php if (!$row['completed']): ?>
                    <form method="post" action="completed.php">
                        <input type="hidden" name="task_id" value="<?php echo $row['id'] ?>">
                        <button type="submit">Mark as Completed</button>
                    </form>
                    <?php endif ?>
                </td>
            </tr>
            <?php endwhile ?>
        </tbody>
    </table>
</body>
</html>