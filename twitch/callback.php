<?php
session_start();

// set the client ID and client secret
$client_id = "";
$client_secret = "";

// set the redirect URI to this page's URL
$redirect_uri = "https://yourlist.online/twitch/callback.php";

// check if the state parameter is set and matches the session state
if (isset($_GET["state"]) && $_GET["state"] === $_SESSION["twitch_state"]) {
  // exchange the authorization code for an access token
  $code = $_GET["code"];
  $url = "https://id.twitch.tv/oauth2/token";
  $data = [
    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "code" => $code,
    "grant_type" => "authorization_code",
    "redirect_uri" => $redirect_uri,
  ];
  $options = [
    "http" => [
      "method" => "POST",
      "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
      "content" => http_build_query($data),
    ],
  ];
  $context = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  $token = json_decode($result, true);

  // set the access token in the session
  $_SESSION["twitch_access_token"] = $token["access_token"];

  // redirect the user to the home page or wherever you want to send them after login
  header("Location: /");
  exit();
} else {
  // handle an invalid state parameter
  echo "Invalid state parameter.";
}
?>
