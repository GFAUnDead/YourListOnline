<?php
    // Step 1: Get the authorization code from the Twitch API
    $code = $_GET['code'];

    // Step 2: Exchange the authorization code for an access token
    $client_id = "";
    $client_secret = "";
    $redirect_uri = "https://yourlist.online/twitch/callback.php";
    $url = "https://id.twitch.tv/oauth2/token";
    $data = array(
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "grant_type" => "authorization_code",
        "redirect_uri" => $redirect_uri,
        "code" => $code
    );
    $options = array(
        "http" => array(
            "header" => "Content-Type: application/x-www-form-urlencoded\r\n",
            "method" => "POST",
            "content" => http_build_query($data),
            "ignore_errors" => true
        )
    );
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $result = json_decode($response, true);
    $access_token = $result['access_token'];

    // Step 3: Use the access token to get the user ID from the Twitch API
    $url = "https://api.twitch.tv/helix/users";
    $options = array(
        "http" => array(
            "header" => "Authorization: Bearer " . $access_token . "\r\nClient-ID: " . $client_id . "\r\n",
            "method" => "GET",
            "ignore_errors" => true
        )
    );
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $result = json_decode($response, true);
    $user_id = $result['data'][0]['id'];

    // Step 4: Store the user ID and access token in your database
    $mysqli = new mysqli("localhost", "username", "password", "database");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }
    $query = "INSERT INTO twitch_users (user_id, access_token) VALUES ('$user_id', '$access_token')";
    if ($mysqli->query($query) === TRUE) {
        session_start();
        $_SESSION['twitchlogged_in'] = true;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . $mysqli->error;
    }
    $mysqli->close();
?>
