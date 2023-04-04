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
$sql = "SELECT * FROM todos WHERE user_id = $user_id ORDER BY created_at DESC";
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
                <li><a href="completed.php">Completed</a></li>
                <li><a href="update.php">Update</a></li>
                <li class="active"><a href="remove.php">Remove</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    <h1>Remove Item</h1>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['title'] ?></td>
                    <td><?= $row['description'] ?></td>
                    <td><?= $row['created_at'] ?></td>
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
