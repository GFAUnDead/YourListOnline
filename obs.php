<?php
// Require database connection
require_once "lists/db_connect.php";

function displayError($message) {
    echo $message;
    exit;
}

if (!isset($_GET['api']) || empty($_GET['api'])) {
    displayError("Please provide your API key in the URL like this: obs.php?api=API_KEY\nGet your API Key from your <a href='https://access.yourlist.online/profile.php'>profile</a>");
}

$api_key = $_GET['api'];

// Check if API key is valid
$stmt = $conn->prepare("SELECT id FROM users WHERE api_key = ?");
$stmt->bind_param("s", $api_key);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $user_id = $user['id'];

    $category_id = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : "1";

    // Get category name
    $stmt = $conn->prepare("SELECT category FROM categories WHERE id = ?");
    $stmt->bind_param("s", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc()['category'];

        // Get tasks for the user in the specified category
        $stmt = $conn->prepare("SELECT id, objective, completed FROM todos WHERE user_id = ? AND category = ? ORDER BY id ASC");
        $stmt->bind_param("is", $user_id, $category_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tasks = $result->fetch_all(MYSQLI_ASSOC);

        $listType = ($list === 'Numbered') ? 'ol' : 'ul';

        echo "<!DOCTYPE html>
            <html>
            <head>
                <title>OBS TASK LIST</title>
                <link rel='icon' href='https://cdn.yourlist.online/img/logo.png' type='image/png' />
                <link rel='apple-touch-icon' href='https://cdn.yourlist.online/img/logo.png'>
                <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
                <script src='https://cdn.yourlist.online/js/about.js'></script>
                <meta http-equiv='refresh' content='10'>
                <style>
                    body {
                        font-family: $font;
                        color: $color;
                    }
                </style>
            </head>
            <body>
                <h1>$category List:</h1>
                <$listType>";

        foreach ($tasks as $task) {
            $task_id = $task['id'];
            $objective = $task['objective'];
            $completed = $task['completed'];

            if ($completed == 'Yes') {
                $taskStyle = $bold == 1 ? 'style="font-size: ' . $font_size . 'px;"><s><strong>' : 'style="font-size: ' . $font_size . 'px;"><s>';
                echo "<li $taskStyle" . htmlspecialchars($objective) . "</s></li>";
            } else {
                $taskStyle = $bold == 1 ? 'style="font-size: ' . $font_size . 'px;"><strong>' : 'style="font-size: ' . $font_size . 'px;">';
                echo "<li $taskStyle" . htmlspecialchars($objective) . "</li>";
            }
        }

        echo "</$listType>
            </body>
            </html>";
    } else {
        displayError("Invalid category ID.");
    }
} else {
    displayError("Invalid API key.");
}
?>
