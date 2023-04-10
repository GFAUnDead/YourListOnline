<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}

// Require database connection
require_once "db_connect.php";

// Initialize variables
$category = "";
$category_err = "";

// Process form submission when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate category
    if (empty(trim($_POST["category"]))) {
        $category_err = "Please enter a category name.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM categories WHERE category = ?";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_category);

            // Set parameters
            $param_category = trim($_POST["category"]);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Store result
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    $category_err = "This category name already exists.";
                } else {
                    $category = trim($_POST["category"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }

    // Check input errors before inserting into database
    if (empty($category_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO categories (category) VALUES (?)";

        if ($stmt = $mysqli->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_category);

            // Set parameters
            $param_category = $category;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to categories page
                header("location: categories.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        $stmt->close();
    }

    // Close connection
    $mysqli->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>YourListOnline - Add Category</title>
    <link rel="icon" href="img/logo.png" type="image/png" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/about.js"></script>
    <style type="text/css">
      body {
        font: 14px sans-serif;
      }
      .wrapper {
        width: 350px; padding: 20px;
      }
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
        <a class="navbar-brand" href="../index.php">YourListOnline</a>
        </div>
        <ul class="nav navbar-nav">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="insert.php">Add</a></li>
            <li><a href="completed.php">Completed</a></li>
            <li><a href="update.php">Update</a></li>
            <li><a href="remove.php">Remove</a></li>
            <li><a href="profile.php">Profile</a></li>
        </ul>
        <p class="navbar-text navbar-right"><a class="popup-link" onclick="showPopup()">&copy; <?php echo date("Y"); ?> YourListOnline. All rights reserved.</a></p>
    </div>
</nav>
<div class="wrapper">
    <h2>Add Category</h2>
    <p>Please fill in the form below to add a new category.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group <?php echo (!empty($category_err)) ? 'has-error' : ''; ?>">
            <label>Category</label>
            <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($category); ?>">
            <span class="help-block"><?php echo $category_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="categories.php" class="btn btn-default">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>