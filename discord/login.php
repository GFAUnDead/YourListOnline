<?php
// Set your client ID and secret
$client_id = '1084995690855407676';
$client_secret = 'jG8qFOhmx3R4o7SarLPHZ64VPPGkrb7K';

// Set your redirect URI
$redirect_uri = 'https://yourlist.online/discord/login.php';

// Set the Discord API endpoints
$discord_authorize_url = 'https://discordapp.com/oauth2/authorize';
$discord_token_url = 'https://discordapp.com/api/oauth2/token';
$discord_user_url = 'https://discordapp.com/api/users/@me';

// If the user is already logged in, redirect them to the homepage
if (isset($_SESSION['access_token'])) {
	header('Location: dashboard.php');
	exit;
}

// If the user clicked the "Login with Discord" button, redirect them to the Discord authorization page
if (isset($_GET['code'])) {
	// Exchange the authorization code for an access token
	$code = $_GET['code'];
	$post_data = array(
		'client_id' => $client_id,
		'client_secret' => $client_secret,
		'grant_type' => 'authorization_code',
		'code' => $code,
		'redirect_uri' => $redirect_uri,
		'scope' => 'identify'
	);
	$options = array(
		'http' => array(
			'header' => 'Content-Type: application/x-www-form-urlencoded',
			'method' => 'POST',
			'content' => http_build_query($post_data)
		)
	);
	$context = stream_context_create($options);
	$response = file_get_contents($discord_token_url, false, $context);
	$token_data = json_decode($response, true);

	// Retrieve the user's Discord ID and username
	$access_token = $token_data['access_token'];
	$options = array(
		'http' => array(
			'header' => 'Authorization: Bearer ' . $access_token,
			'method' => 'GET'
		)
	);
	$context = stream_context_create($options);
	$response = file_get_contents($discord_user_url, false, $context);
	$user_data = json_decode($response, true);
	$discord_id = $user_data['id'];
	$username = $user_data['username'] . '#' . $user_data['discriminator'];

	// Store the access token and user information in the session
	$_SESSION['access_token'] = $access_token;
	$_SESSION['discord_id'] = $discord_id;
	$_SESSION['username'] = $username;

	// Redirect the user to the homepage
	header('Location: dashboard.php');
	exit;
}

// If the user hasn't clicked the "Login with Discord" button yet, display the login page
$discord_login_url = $discord_authorize_url . '?client_id=' . $client_id . '&redirect_uri=' . $redirect_uri . '&response_type=code&scope=identify';