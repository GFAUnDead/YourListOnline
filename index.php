<!DOCTYPE html>
<html>
<head>
	<title>YourListOnline - HOME</title>
	<link rel="icon" href="img/logo.png" type="image/png" />
	<link rel="apple-touch-icon" href="img/logo.png">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<script src="js/about.js"></script>
	<style type="text/css">
    a.popup-link {
      text-decoration: none;
      color: black;
      cursor: pointer;
    }
  </style>
</head>
<body>
	<header>
		<h1>Welcome to Your List Online</h1>
	</header>
	<main>
		<p>With this website you can keep track of all the tasks you need to complete.</p>
		<p>To get started, simply log in with your Twitch account.</p>
		<a href="lists/dashboard.php"><button class="button">Login</button></a>
		<a href=""><button class="twitch-button">Login with Twitch (COMING SOON)</button></a>
		<a href=""><button class="discord-button">Login with Discord (COMING SOON)</button></a>
		<br><br>
		<p><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
	</main>
</body>
</html>
