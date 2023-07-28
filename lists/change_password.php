<?php
// Initialize the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

// Require database connection
require_once "db_connect.php";
// Fetch the user's data from the database
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get the current hour in 24-hour format (0-23)
$currentHour = date('G');
// Initialize the greeting variable
$greeting = '';
// Check if it's before 12 PM (noon)
if ($currentHour < 12) {
    $greeting = "Good morning";
} else {
    $greeting = "Good afternoon";
}

// Define variables and initialize with empty values
$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate current password
    if (empty(trim($_POST["current_password"]))) {
        $current_password_err = "Please enter your current password.";
    } else {
        $current_password = trim($_POST["current_password"]);
    }

    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter a new password.";
    } elseif (strlen(trim($_POST["new_password"])) < 8) {
        $new_password_err = "Password must have at least 8 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the new password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password !== $confirm_password)) {
            $confirm_password_err = "Passwords do not match.";
        }
    }

// Check input errors before updating the database
if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
    // Check current password
    $sql = "SELECT password FROM users WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                if (mysqli_num_rows($result) == 1) {
                    $row = mysqli_fetch_assoc($result);
                    $hashed_password = $row["password"];
                    if (password_verify($current_password, $hashed_password)) {
                        // Update password
                        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
                        $stmt = mysqli_prepare($conn, $sql);
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "ss", $hashed_new_password, $user_id);
                            if (mysqli_stmt_execute($stmt)) {
                                if ($stmt->affected_rows > 0) {
                                    // Password updated successfully, redirect to login page
                                    header("Location: logout.php");
                                    exit();
                                } else {
                                    $error_message = "Oops! Something went wrong. Please try again later.";
                                }
                            } else {
                                $error_message = "Error executing the password update query: " . mysqli_stmt_error($stmt);
                            }
                        } else {
                            $error_message = "Error preparing the password update statement: " . mysqli_error($conn);
                        }
                    } else {
                        $current_password_err = "The password you entered is not valid.";
                    }
                } else {
                    $error_message = "User not found or multiple users with the same ID exist.";
                }
            } else {
                $error_message = "Error fetching the result of the password select query: " . mysqli_error($conn);
            }
        } else {
            $error_message = "Error executing the password select query: " . mysqli_stmt_error($stmt);
        }
    } else {
        $error_message = "Error preparing the password select statement: " . mysqli_error($conn);
    }
}

    // Close database connection
    mysqli_close($conn);
    header("Location: logout.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Change Password</title>
    <link rel="icon" href="https://cdn.yourlist.online/img/logo.png" type="image/png" />
    <link rel="apple-touch-icon" href="https://cdn.yourlist.online/img/logo.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.yourlist.online/css/list.css">
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
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="https://yourlist.online/">YourListOnline</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="insert.php">Add</a></li>
            <li><a href="remove.php">Remove</a></li>
            <li class="dropdown dropdown-hover">
                <a class="dropdown" data-toggle="dropdown">Update <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="update_objective.php">Update Objective</a></li>
                    <li><a href="update_category.php">Update Objective Category</a></li>
                </ul>
            </li>
            <li><a href="completed.php">Completed</a></li>
            <li class="dropdown dropdown-hover">
                <a class="dropdown" data-toggle="dropdown">Categories <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="categories.php">View Categories</a></li>
                    <li><a href="add_category.php">Add Category</a></li>
                </ul>
            </li>
            <li class="dropdown dropdown-hover">
			      <a class="dropdown" data-toggle="dropdown">Profile <span class="caret"></span></a>
			      	<ul class="dropdown-menu">
			      		<li><a href="profile.php">View Profile</a></li>
			      		<li><a href="update_profile.php">Update Profile</a></li>
                        <li><a href="obs_options.php">OBS Viewing Options</a></li>
                        <li><a href="logout.php">Logout</a></li>
			      	</ul>
            </li>
            <?php if ($_SESSION['is_admin']) { ?>
            <li class="dropdown dropdown-hover">
			      <a class="dropdown" data-toggle="dropdown">Admins <span class="caret"></span></a>
			      	<ul class="dropdown-menu">
                <li><a href="admins/dashboard.php">Admin Dashboard</a></li>
			      	</ul>
            </li>
            <?php } ?>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
</nav>
<div class="col-md-6">
<h1><?php echo "$greeting, $username!"; ?></h1>
<h2>Change Your Password</h2>
<?php if (isset($error_message)) { ?>
    <div class="alert alert-danger"><?php echo $error_message; ?></div>
<?php } ?>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="form-group">
        <label for="current_password">Current Password:</label>
        <input type="password" class="form-control" id="current_password" name="current_password" required>
    </div>
    <div class="form-group">
        <label for="new_password">New Password:</label>
        <input type="password" class="form-control" id="new_password" name="new_password" required>
    </div>
    <div class="form-group">
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
</form>
</div>
</body>
</html>