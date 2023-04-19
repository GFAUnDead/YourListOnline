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
    $username = $result['data'][0]['login'];

    // Step 4: Store the user ID and access token in your database
    $mysqli = new mysqli("localhost", "username", "password", "database");
    if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: " . $mysqli->connect_error;
        exit();
    }

    // Generate an API key for the user
    $api_key = bin2hex(random_bytes(20));
    $expires_at = date('Y-m-d H:i:s', strtotime('+30 days'));

    // Insert user data into the database
    $query = "INSERT INTO twitch_users (username, is_admin, api_key, access_token, refresh_token, expires_at, signup_date, last_login) VALUES ('$username', 0, '$api_key', '$access_token', '', '$expires_at', NOW(), NOW())";
    if ($mysqli->query($query) === TRUE) {
        // Start the session and set the twitch_logged_in flag
        session_start();
        $_SESSION['twitch_logged_in'] = true;
        $_SESSION['twitch_username'] = $username;

        // Redirect to the dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $query . "<br>" . $mysqli->error;
    }
    $mysqli->close();
?>
