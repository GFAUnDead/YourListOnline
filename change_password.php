<?php
// Initialize the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit;
}
 
// Include config file
require_once "db_connect.php";
 
// Define variables and initialize with empty values
$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate current password
    if(empty(trim($_POST["current_password"]))){
        $current_password_err = "Please enter your current password.";
    } else{
        $current_password = trim($_POST["current_password"]);
    }
    
    // Validate new password
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Please enter a new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Password must have at least 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before updating the database
    if(empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)){
        
        // Prepare a select statement
        $sql = "SELECT password FROM users WHERE id = ?";
        
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $param_id);
            
            // Set parameters
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                $stmt->store_result();
                
                // Check if current password is correct
                if($stmt->num_rows == 1){
                    $stmt->bind_result($hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($current_password, $hashed_password)){
                            // Password is correct, update password
                            $sql = "UPDATE users SET password = ? WHERE id = ?";
                            
                            if($stmt = $conn->prepare($sql)){
                                // Bind variables to the prepared statement as parameters
                                $stmt->bind_param("si", $param_password, $param_id);
                                
                                // Set parameters
                                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
                                
                                // Attempt to execute the prepared statement
                                if($stmt->execute()){
                                    // Password updated successfully, destroy session and redirect to login page
                                    session_destroy();
                                    header("location: login.php");
                                    exit();
                                } else{
                                    echo "Oops! Something went wrong. Please try again later.";
                                }
                                
                                // Close statement
                                $stmt->close();
                            }
                        } else{
                            $current_password_err = "Current password is incorrect.";
                        }
                    }
                } else{
                    // Redirect to login page if user session is not found
                    header("location: login.php");
                    exit();
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            $stmt->close();
        }
    }
    
    // Close connection
    $conn->close();
}
?>