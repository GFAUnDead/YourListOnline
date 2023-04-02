<!DOCTYPE html>
<html>
<head>
	<title>To Do List - Update</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<link rel="icon" href="../../images/logo.png" type="image/png" />
</head>
<body>
	<header>
		<div class="header-title">
			<h1>To Do List</h1>
		</div>
		<div class="menu">
            <button onclick="location.href='todo.php'">Home</button>
		</div>
	</header>

    <div class="container">
        <br>
        <?php
            // Check if the API key is provided in the URL
            if (!isset($_GET['api'])) {
                echo "API key is missing.";
                exit();
            }
            
            // Retrieve the API key from the URL
            $api_key = $_GET['api'];

            // Connect to the API keys database
            $servername = "(REDACTED)";
            $username = "(REDACTED)";
            $password = "(REDACTED)";
            $dbname = "(REDACTED)";
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check if the connection is successful
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            // Prepare the SQL statement to retrieve the channel name from the database
            $stmt = $conn->prepare("SELECT channelname FROM allowed_users WHERE api_key = ?");
            $stmt->bind_param("s", $api_key);

            // Execute the SQL statement
            $stmt->execute();

            // Bind the result to variables
            $stmt->bind_result($channelname);

            // Fetch the result
            $stmt->fetch();

            // Close the statement
            $stmt->close();

            // Close the database connection
            $conn->close();

            // Check if the provided API key is valid and retrieve the channel name from the database
            if (empty($channelname)) {
                // Return an error message if the API key is not valid
                echo "Invalid API key.";
                exit();
            }

            // Connect to the database
            $servername = "(REDACTED)";
            $username = "(REDACTED)";
            $password = "(REDACTED)";
            $dbname = "(REDACTED)";

            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check if the connection is successful
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            // Prepare the SQL statement to retrieve the user's tasks from the database
            $stmt = $conn->prepare("SELECT id, todo_text FROM todos WHERE user_id=(SELECT id FROM users WHERE name=?)");
            $stmt->bind_param("s", $channelname);

            // Execute the SQL statement
            $stmt->execute();

            // Bind the result to variables
            $stmt->bind_result($task_id, $task_text);

            // Output the tasks in an HTML table
            echo "<table>";
            echo "<tr><th>Task ID</th><th>Task Text</th></tr>";
            while ($stmt->fetch()) {
                echo "<tr><td>$task_id</td><td>$task_text</td></tr>";
            }
            echo "</table>";
            // Close the statement
            $stmt->close();

            // Close the database connection
            $conn->close();

            // Connect to the database
            $servername = "(REDACTED)";
            $username = "(REDACTED)";
            $password = "(REDACTED)";
            $dbname = "(REDACTED)";
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Check if the connection is successful
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            // Prepare the SQL statement to retrieve the user's ID from the database
            $stmt = $conn->prepare("SELECT id FROM users WHERE name = ?");
            $stmt->bind_param("s", $channelname);

            // Execute the SQL statement
            $stmt->execute();

            // Bind the result to variables
            $stmt->bind_result($current_user_id);

            // Fetch the result
            $stmt->fetch();

            // Close the statement
            $stmt->close();

            // Check if the form was submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST");
                // Retrieve the task ID and task text from the form
                $task_id = $_POST["todo_id"];
                $task_text = $_POST["todo_text"];

            // Prepare the SQL statement to retrieve the user ID associated with the task
            $stmt = $conn->prepare("SELECT users.id FROM users INNER JOIN todos ON users.id = todos.user_id WHERE todos.id = ?");
            $stmt->bind_param("i", $task_id);

            // Execute the SQL statement
            $stmt->execute();

            // Bind the result to variables
            $stmt->bind_result($user_id);

            // Fetch the result
            $stmt->fetch();

            // Close the statement
            $stmt->close();

            // Check if the task belongs to the user
            if ($user_id != $channel_id) {
                // Return an error message if the task does not belong to the user
                echo "You are not authorized to update this task.";
                exit();
            }

            echo "<form method='POST'>";
            echo "<label for='todo_id'>Task ID:</label>";
            echo "<input type='number' name='todo_id' required><br><br>";
            echo "<label for='todo_text'>Task Text:</label>";
            echo "<input type='text' name='todo_text' required><br><br>";
            echo "<input type='submit' value='Update Task'>";
            echo "</form>";
        ?>
	</div>
</body>
</html>