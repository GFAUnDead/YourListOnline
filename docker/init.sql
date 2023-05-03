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

CREATE TABLE categories (
    id INT(255) AUTO_INCREMENT,
    category VARCHAR(255) COLLATE utf8mb4_unicode_ci,
    PRIMARY KEY (id)
);
