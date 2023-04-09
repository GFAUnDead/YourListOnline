<!DOCTYPE html>
<html>
<head>
    <title>OBS TASK LIST</title>
    <meta http-equiv="refresh" content="10">
</head>
<body>
    <?php
    // Require database connection
    require_once "lists/db_connect.php";

    if (!isset($_GET['api']) || empty($_GET['api'])) {
        // Display missing API Key Error
        echo "Please provide your API key in the URL like this: obs.php?api=API_KEY";
        echo "<br>Get your API Key from your <a href='profile.php'>profile</a>";
        exit;
    }

    $api_key = $_GET['api'];
    // Check if API key is valid
    $stmt = $conn->prepare("SELECT * FROM users WHERE api_key = ?");
    $stmt->bind_param("s", $api_key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        // Get tasks for user
        $stmt = $conn->prepare("SELECT * FROM todos WHERE user_id = ? ORDER BY id DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        // Invalid API key, show error message
        $error_message = "Invalid API key";
    }

    if (isset($error_message)) {
        echo $error_message;
    } else {
        // Display tasks
        echo "<h1>Current Task List:</h1>";
        echo "<ul>";
        foreach ($tasks as $task) {
            $task_id = $task['id'];
            $objective = $task['objective'];
            $completed = $task['completed'];
            if ($completed == 'Yes') {
                echo "<li><s>" . htmlspecialchars($objective) . "</s></li>";
            } else {
                echo "<li>" . htmlspecialchars($objective) . "</li>";
            }
        }
        echo "</ul>";
    }
    ?>
</body>
</html>
