<?php ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); ?>
<!DOCTYPE html>
<html>
<head>
    <title>OBS TASK LIST</title>
    <link rel="icon" href="img/logo.png" type="image/png" />
    <link rel="apple-touch-icon" href="img/logo.png">
    <meta http-equiv="refresh" content="10">
</head>
<body>
    <?php
    // Require database connection
    require_once "lists/db_connect.php";

    if (!isset($_GET['api']) || empty($_GET['api'])) {
        // Display missing API Key Error
        echo "Please provide your API key in the URL like this: obs.php?api=API_KEY";
        echo "<br>Get your API Key from your <a href='lists/profile.php'>profile</a>";
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
        // Get category id from url, default to "Default" category if not provided
        $category_id = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : "1";
        // Get category name
        $stmt = $conn->prepare("SELECT category FROM categories WHERE id = ?");
        $stmt->bind_param("s", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $category = $result->fetch_assoc()['category'];
            // Get tasks for user in the specified category
            $stmt = $conn->prepare("SELECT * FROM todos WHERE user_id = ? AND category = ? ORDER BY id ASC");
            $stmt->bind_param("is", $user_id, $category_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $tasks = $result->fetch_all(MYSQLI_ASSOC);
            echo "<h1>Current Task List:</h1>";
            echo "<p>Showing tasks from category: $category</p>";
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
        } else {
            // Invalid category id, show error message
            echo "Invalid category id";
        }
    } else {
        // Invalid API key, show error message
        $error_message = "Invalid API key";
        echo $error_message;
    }
    ?>
</body>
</html>