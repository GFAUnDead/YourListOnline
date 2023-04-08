# YourListOnline
YourListOnline is a website that allows you to keep track of all the tasks you need to complete for your streaming or normal day-to-day activities.

## Getting Started
To get started, you will need to create a SQL database and use the following code to build the tables:

```sql
CREATE TABLE users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) COLLATE latin1_swedish_ci,
    password VARCHAR(255) COLLATE latin1_swedish_ci,
    api_key VARCHAR(255) COLLATE latin1_swedish_ci,
    is_admin TINYINT(1) DEFAULT 0,
    signup_date DATETIME,
    last_login DATETIME
);

CREATE TABLE todos (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    user_id INT(11),
    objective TEXT COLLATE latin1_swedish_ci,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed TINYTEXT COLLATE latin1_swedish_ci
);
```

The todos table has the following columns:

* id: a unique identifier for the todo item
* user_id: the id of the user who owns the todo item
* objective: the text of the todo item
* created_at: the timestamp when the todo item was created
* updated_at: the timestamp when the todo item was last updated
* completed: a flag indicating whether the todo item has been completed or not definded by "Yes" or "No"

Note that id is set to INT with the AUTO_INCREMENT option, which will automatically generate a unique identifier for each new todo item added to the table. user_id and objective are set to INT and TEXT data types, respectively. created_at and updated_at are set to TIMESTAMP data type to store the date and time values, and completed is set to TINYTEXT data type to store a flag that indicates whether the todo item has been completed or not.

## Database Connection Settings
After you've created the database and tables, you'll have to add those deatils in the *db_connect.php* file.
```php
<?php
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "todolistdb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```