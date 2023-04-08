<?php
// Require database connection
require_once "db_connect.php";

if (isset($_GET['api'])) {
    $api_key = $_GET['api'];
    // Check if the API key is valid
    $stmt = $conn->prepare("SELECT * FROM USERS WHERE api_key = ?");
    $stmt->bind_param("s", $api_key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        // Get tasks for user
        $stmt = $conn->prepare("SELECT * FROM todos WHERE user_id = ? ORDER BY id ASC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // Invalid API Key, showing the error message
        $error_message = "Invalid API Key";
    }
} else {
    // API Key is not found in the database, show the error message
    $error_message = "The API Key is not found.";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>OBS TASK LIST</title>
</head>
<body>
<h1>Current Task List:</h1>
<ul>
    <?php while ($row = $results->fetch_assoc()); ?>
    <li><?php echo htmlspecialchars($row['objective']); ?></li>
    <?php endwhile; ?>
</ul>
</body>
</html>