<!DOCTYPE html>
<html>
<head>
	<title>YourListOnline - HOME</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<header>
		<h1>Welcome to Your List Online</h1>
	</header>
	
	<main>
		<p>With this website you can keep track of all the tasks you need to complete.</p>
		<p>To get started, simply log in with your Twitch account.</p>
		<a href="https://id.twitch.tv/oauth2/authorize?response_type=code&client_id=YOUR_CLIENT_ID&redirect_uri=YOUR_REDIRECT_URI&scope=user:read:email"><button class="twitch-button">Log in with Twitch</button></a>
		<a href="todo.php"><button class="twitch-button">OLD SYSTEM LOGIN</button></a>
	</main>

	<footer>
		<p>&copy; <?php echo date("Y"); ?> My To-Do List. All rights reserved.</p>
	</footer>
</body>
</html>
