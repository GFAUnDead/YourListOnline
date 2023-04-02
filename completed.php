<!DOCTYPE html>
<html>
<head>
	<title>To Do List - Completed</title>
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

            // Prepare the SQL statement to retrieve the user's tasks from the database
            $stmt = $conn->prepare("SELECT id, todo_text, completed FROM todos WHERE user_id=(SELECT id FROM users WHERE name=?)");
            $stmt->bind_param("s", $channelname);

            // Execute the SQL statement
            $stmt->execute();

            // Bind the result to variables
            $stmt->bind_result($task_id, $task_text, $completed);

            // Output the tasks in an HTML table
            echo "<table id='todo-table'>";
            echo "<tr><th>Task ID</th><th>Task Text</th><th>Completed</th><th>Action</th></tr>";
            while ($stmt->fetch()) {
                echo "<tr><td>$task_id</td><td>$task_text</td><td>$completed</td><td>";
                if ($completed == 0) {
                    echo "<form method='POST' action='completed.php?api=$api_key'>";
                    echo "<input type='hidden' name='todo_id' value='$task_id'>";
                    echo "<input type='hidden' name='todo_text' value='$task_text'>";
                    echo "<input type='submit' name='complete' value='Mark Completed'>";
                    echo "</form>";
                }
                echo "</td></tr>";
            }
            echo "</table>";

            // Close the statement
            $stmt->close();

            // Check if the form was submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Retrieve the task ID and task text from the form
                $task_id = $_POST["todo_id"];
                $task_text = $_POST["todo_text"];
            
                // Prepare the SQL statement to update the task as completed in the database
                $stmt = $conn->prepare("UPDATE todos SET completed = 'true' WHERE id = ?");
                $stmt->bind_param("i", $task_id);
            
                // Execute the SQL statement
                $stmt->execute();
            
                // Close the statement
                $stmt->close();
            }
        ?>
        <script>
          // Add an event listener for form submission
          document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
              // Reload the page after a short delay
              setTimeout(() => location.reload(), 1000);
            });
        });
        </script>
	</div>
</body>
</html>