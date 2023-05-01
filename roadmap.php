<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>YourListOnline - Road Map</title>
	<link rel="icon" href="img/logo.png" type="image/png" />
	<link rel="apple-touch-icon" href="img/logo.png">
	<link rel="stylesheet" href="css/roadmap.css">
</head>
<body>
	<div class="container">
		<h1>Welcome to Your List Online Road Map</h1>
		<p>We're currently looking to add the following:</p>
		<ul>
			<li><a href="?info=FormattingStyling" class="info-link">Formatting and Styling</a></li>
			<li><a href="?info=ColabList" class="info-link">Colab List</a></li>
			<li><a href="?info=RegisterLoginPage" class="info-link">Register/Login Page</a></li>
			<li><a href="?info=MobileSupport" class="info-link">Mobile Support</a></li>
			<li><a href="?info=TwitchLoginSupport" class="info-link">Twitch Login Support</a></li>
		</ul>
		<div class="info-info">
			<?php
				if (isset($_GET['info'])) {
					$info = $_GET['info'];
					switch ($info) {
						case 'FormattingStyling':
							echo "<p>Allow the use of colours for the OBS link to make it stand out.<br>";
							echo "Use bold, underline or italics to emphasize important information in the text.";
							echo "Use dot points or numbered points to organize information and make it easy to follow.</p>";
							break;
						case 'ColabList':
							echo "<p>Create a list of collaborators and their roles.<br>";
							echo "Define the tasks and responsibilities of each collaborator.";
							echo "Set deadlines for each task.</p>";
							break;
						case 'RegisterLoginPage':
							echo "<p>Implement automatic profile image generation for users who register or login.<br>";
							echo "Allow users to choose a profile picture from their device or social media profiles.</p>";
							break;
						case 'MobileSupport':
							echo "<p>Optimize the website for mobile devices, making it easy to navigate and use.<br>";
							echo "Use responsive design to ensure that the website looks good on a variety of screen sizes.";
							echo "Test the website on multiple mobile devices to ensure it works as expected.</p>";
							break;
						case 'TwitchLoginSupport':
							echo "<p>Allow users to login with their Twitch account.<br>";
							echo "Use Twitch's API to retrieve user information and display it on the website.</p>";
							break;
						default:
							echo "<p>Please select an category above to view it's information.</p>";
					}
				} else {
					echo "<p>Please select an category above to view it's information.</p>";
				}
			?>
		</div>
	</div>
</body>
</html>