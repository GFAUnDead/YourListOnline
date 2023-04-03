<!DOCTYPE html>
<html>
<head>
	<title>YourListOnline - Remove</title>
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
			include('db_connect.php');

            // Check if the connection is successful
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Check if the form is submitted
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Retrieve the task ID and user ID from the form data
                $task_id = $_POST['todo_id'];
                $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
            
                if (!empty($user_id)) {
                    // Prepare the SQL statement to delete the task from the database
                    $stmt = $conn->prepare("DELETE FROM todos WHERE id = ? AND user_id = ?");
                    $stmt->bind_param("ii", $task_id, $user_id);
                
                    // Execute the SQL statement
                    if ($stmt->execute()) {
                        if ($stmt->affected_rows > 0) {
                            echo "Task deleted successfully.";
                        } else {
                            echo "You are not authorized to delete this task.";
                        }
                    } else {
                        echo "Error deleting task: " . $stmt->error;
                    }
                
                    // Close the statement
                    $stmt->close();
                } else {
                    echo "User ID is missing.";
                }
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
            
            // Connect to the database
			include('db_connect.php');

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

            echo "<form method='POST'>";
            echo "<label for='todo_id'>Task ID:</label>";
            echo "<input type='number' name='todo_id' required><br><br>";
            echo "<input type='hidden' name='user_id' value='$user_id'>";
            echo "<input type='submit' name='delete' value='Delete Task'>";
            echo "</form>";
        ?>
	</div>
</body>
</html>