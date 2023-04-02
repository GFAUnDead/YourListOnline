<!DOCTYPE html>
<html>
<head>
    <title>To Do List Database</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="icon" href="../../images/logo.png" type="image/png" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <div class="header-title">
            <h1>To Do List Database</h1>
        </div>
        <div class="menu">
            <button onclick="location.href='../index.php'">BACK</button>
            <button onclick="location.href='todo.php'">Home</button>
        </div>
    </header>

    <div class="container">
        <br>
        <?php
            // Check if the API key was submitted via a form
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Retrieve the API key from the form data
                $api_key = $_POST['api_key'];
            } else {
                // Display the form to get the API key from the user
                echo '<form method="post">';
                echo '<label for="api_key">API Key:</label>';
                echo '<input type="text" id="api_key" name="api_key">';
                echo '<input type="submit" value="Submit">';
                echo '</form>';
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
            
            // Prepare the SQL statement to retrieve the channel name and username for the given API key
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
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            
            // Retrieve the data from the table for the specified channel name
            $sql = "SELECT todo_text, completed FROM todos WHERE user_id IN (SELECT id FROM users WHERE name='$channelname')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $_GET['channel']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Check if the query was successful
            if (!$result) {
                // If not, log the error and display an error message to the user
                error_log("Database query failed: " . $conn->error);
                echo "An error occurred while retrieving the data. Please try again later.";
                exit();
            }

            // If there are no rows returned, display a message to the user
            if ($result->num_rows === 0) {
                // If not, display a message to the user
                echo "No data found for the specified channel.";
                exit();
            } else {
                echo "<button onclick='location.href='insert.php?api=$api_key''>New</button>";
                echo "<button onclick='location.href='update.php?api=$api_key''>Update</button>";
                echo "<button onclick='location.href='completed.php?api=$api_key''>Done</button>";
                echo "<button onclick='location.href='remove.php?api=$api_key''>Delete</button>";
                echo "<br>";
                echo "<h2>Viewing all available tasks on this page for $channelname:</h2>\r\n";
            
                // Display the search bar and the table of entries
                echo "<form method='GET' action=''>\r\n";
                echo "<input type='text' name='search' id='search' placeholder='Search for your tasks'>\r\n";
                echo "</form>\r\n";
            
                echo "<table>\r\n";
                echo "<tr><th>To Do List</th></tr>\r\n";
            
                while ($row = mysqli_fetch_assoc($result)) {
                    $todo_text = $row['todo_text'];
                    $completed = $row['completed'];
            
                    // Display the table row with the data, and strike out if completed
                    if ($completed == 'true') {
                        echo "<tr><td><s>$todo_text</s></td></tr>\r\n";
                    } else {
                        echo "<tr><td>$todo_text</td></tr>\r\n";
                    }
                }
            
                echo "</table>";
            
                $stmt->close();
                $conn->close();
            }
        ?>
	</div>
</body>
</html>