<!DOCTYPE html>
<html>
<head>
	<title>User Login and Registration</title>
</head>
<body>
	<h2>Login</h2>
	<form method="post" action="login.php">
		<label for="email">Email:</label>
		<input type="email" name="email" required><br><br>
		<label for="password">Password:</label>
		<input type="password" name="password" required><br><br>
		<input type="submit" value="Login">
	</form>
	<br>
	<h2>Register</h2>
	<form method="post" action="register.php">
		<label for="name">Name:</label>
		<input type="text" name="name" required><br><br>
		<label for="email">Email:</label>
		<input type="email" name="email" required><br><br>
		<label for="password">Password:</label>
		<input type="password" name="password" required><br><br>
		<input type="submit" value="Register">
	</form>
</body>
</html>
