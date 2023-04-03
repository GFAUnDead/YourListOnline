<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User exists, set session variables and redirect to dashboard
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: dashboard.php');
    } else {
        // User does not exist, show error message
        $error = 'Invalid email or password';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
	<title>YourListOnline - Login</title>
</head>
<body>
	<h2>Login</h2>
	<?php if (isset($error)) { echo '<p>' . $error . '</p>'; } ?>
	<form method="post" action="">
		<label for="email">Email:</label>
		<input type="email" name="email" required><br><br>
		<label for="password">Password:</label>
		<input type="password" name="password" required><br><br>
		<input type="submit" value="Login">
	</form>
</body>
</html>
