<!DOCTYPE html>
<html>
<head>
<title>OBS TASK LIST</title>
<link rel='icon' href='https://cdn.yourlist.online/img/logo.png' type='image/png' />
<link rel='apple-touch-icon' href='https://cdn.yourlist.online/img/logo.png'>
<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
<?php
// Require database connection
require_once "lists/db_connect.php";

if (isset($_GET['api']) && !empty($_GET['api'])) {
    $api_key = $_GET['api'];

    // Check if API key is valid
    $stmt = $conn->prepare("SELECT id FROM users WHERE api_key = ?");
    $stmt->bind_param("s", $api_key);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $user_id = $user['id'];
        // Retrieve font, font_size, color, list, and shadow settings for the user from the showobs table
        $stmt = $conn->prepare("SELECT * FROM showobs WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $settings = $result->fetch_assoc();
        $font = isset($settings['font']) ? $settings['font'] : null;
        $color = isset($settings['color']) ? $settings['color'] : null;
        $list = isset($settings['list']) ? $settings['list'] : null;
        $shadow = isset($settings['shadow']) ? $settings['shadow'] : null;
        $font_size = isset($settings['font_size']) ? $settings['font_size'] : null;
        $listType = ($list === 'Numbered') ? 'ol' : 'ul';
        $bold = isset($settings['bold']) ? $settings['bold'] : null;
    } else {
        echo "</head>";
        echo "<body>";
        echo "Invalid API key.<br>Get your API Key from your <a href='https://access.yourlist.online/profile.php'>profile</a>.";
        echo "<p>If you wish to define a working category, please add it like this: <strong>obs.php?api=API_KEY&category=1</strong></br>";
        echo "(where ID 1 is called Default defined on the categories page.)</p>";
        echo "</body>";
        echo "</html>";
        exit;
    }
} else {
    echo "</head>";
    echo "<body>";
    echo "<p>Please provide your API key in the URL like this: <strong>obs.php?api=API_KEY</strong></p>";
    echo "<p>Get your API Key from your <a href='https://access.yourlist.online/profile.php'>profile</a>.</p>";
    echo "<p>If you wish to define a working category, please add it like this: <strong>obs.php?api=API_KEY&category=1</strong></br>";
    echo "(where ID 1 is called Default defined on the categories page.)</p>";
    echo "</body>";
    echo "</html>";
    exit;
}

if ($user_id) {
    $category_id = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : "1";

    // Get category name
    $stmt = $conn->prepare("SELECT category FROM categories WHERE id = ?");
    $stmt->bind_param("s", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc()['category'];

        $stmt = $conn->prepare("SELECT * FROM todos WHERE user_id = ? AND category = ? ORDER BY id ASC");
            $stmt->bind_param("is", $user_id, $category_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $tasks = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "</head>";
        echo "<body>";
        echo "Invalid category ID.";
        echo "<br>ID 1 is called Default defined on the categories page, please review this page for a full list of IDs.</p>";
        echo "</body>";
        echo "</html>";
        exit;
    }
}
?>
<meta http-equiv='refresh' content='10'>
<style>
    body {
       <?php
       if ($font) {
           echo "font-family: $font; ";
       }
       if ($color) {
           echo "color: $color;";
           if ($shadow && $shadow == 1) {
               if ($color === 'Black') {
                   echo "text-shadow: 0px 0px 6px White;";
               } elseif ($color === 'White') {
                   echo "text-shadow: 0px 0px 6px Black;";
               } else {
                   echo "text-shadow: 0px 0px 6px Black;";
               }
           }
       }
       ?> }
</style>
</head>
<body>
<h1><?php echo $category; ?> List:</h1>
<?php echo "<$listType>"; foreach ($tasks as $task) { $task_id = $task['id']; $objective = $task['completed'] === 'Yes' ? '<s>' . htmlspecialchars($task['objective']) . '</s>' : htmlspecialchars($task['objective']); $taskStyle = $bold == 1 ? 'style="font-size: ' . $font_size . 'px;"><strong>' : 'style="font-size: ' . $font_size . 'px;">'; echo "<li $taskStyle$objective</li>"; } echo "</$listType>"; ?>

</body>
</html>