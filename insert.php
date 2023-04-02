<!DOCTYPE html>
<html>
<head>
	<title>To Do List - Insert</title>
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

            // Prepare the SQL statement to retrieve the channel name associated with the provided API key
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
            
            // Check if the form to add a task was submitted
            if (isset($_POST['todo_text'])) {
                // Connect to the database
			    include('db_connect.php');

                // Check if the connection is successful
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Prepare the SQL statement to insert the task into the database
                $stmt = $conn->prepare("INSERT INTO todos (user_id, todo_text, completed) VALUES ((SELECT id FROM users WHERE name=?), ?, "false")");
                $stmt->bind_param("ss", $channelname, $_POST['todo_text']);
            
                // Execute the SQL statement
                if ($stmt->execute()) {
                    // Display a success message to the user
                    echo "Task added successfully!";
                } else {
                    // If the SQL statement fails, display an error message to the user
                    echo "An error occurred while adding the task. Please try again later.";
                }

                // Close the statement
                $stmt->close();
            
                // Close the database connection
                $conn->close();
            }
            
            echo "<form method='POST' action=''>";
            echo "<label for='todo_text'>Task:</label><br>";
            echo "<input type='text' id='todo_text' name='todo_text'><br>";
            echo "<input type='submit' value='Add task'>";
            echo "</form>";
            ?>
	</div>
</body>
</html>