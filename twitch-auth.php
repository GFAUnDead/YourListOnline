<?php
session_start();

$client_id = 'YOUR_TWITCH_CLIENT_ID';
$client_secret = 'YOUR_TWITCH_CLIENT_SECRET';
$redirect_uri = 'https://URL/twitch-auth.php';

if (isset($_GET['code'])) {
  $code = $_GET['code'];

  $url = 'https://id.twitch.tv/oauth2/token';
  $data = array(
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'code' => $code,
    'grant_type' => 'authorization_code',
    'redirect_uri' => $redirect_uri
  );

  $options = array(
    'http' => array(
      'header' => "Content-type: application/x-www-form-urlencoded\r\n",
      'method' => 'POST',
      'content' => http_build_query($data),
    ),
  );

  $context = stream_context_create($options);
  $response = file_get_contents($url, false, $context);

  if ($response) {
    $response = json_decode($response, true);
    $access_token = $response['access_token'];

    // Save access token in session
    $_SESSION['access_token'] = $access_token;

    // Redirect back to home page
    header('Location: https://URL/');
    exit;
  } else {
    echo 'Error getting access token';
  }
} else {
  echo 'Invalid authorization code';
}
