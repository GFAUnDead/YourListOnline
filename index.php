<!DOCTYPE html>
<html>
<head>
	<title>YourListOnline - HOME</title>
  	<link rel="icon" href="https://cdn.yourlist.online/img/logo.png" type="image/png" />
  	<link rel="apple-touch-icon" href="https://cdn.yourlist.online/img/logo.png">
  	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  	<link rel="stylesheet" href="https://cdn.yourlist.online/css/home.css">
  	<script src="https://cdn.yourlist.online/js/about.js"></script>
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
		<p>To get started, simply log in with your account or use your Twitch or Discord account.</p>
		<a href="https://access.yourlist.online/dashboard.php"><button class="button">Login</button></a>
		<a href=""><button class="twitch-button">Login with Twitch (COMING SOON)</button></a>
		<a href=""><button class="discord-button">Login with Discord (COMING SOON)</button></a>
		<br><br>
		<p><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
	</main>
</body>
</html>
