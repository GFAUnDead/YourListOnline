<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once "db_connect.php";

// Mark task as completed
if (isset($_GET['task_id'])) {
    $task_id = $_GET['task_id'];
    $user_id = $_SESSION['user_id'];

    $sql = "UPDATE todos SET completed = 1 WHERE id = ? AND user_id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Get user's to-do list
$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM todos WHERE user_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

?>

<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Completed</title>
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">YourListOnline</a>
            </div>
            <ul class="nav navbar-nav">
                <li><a href="dashboard.php">Dashboard</a></li>
                <li class="active"><a href="completed.php">Completed</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>
    
    <h1>My To-Do List</h1>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Completed</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['title']) ?></td>
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
