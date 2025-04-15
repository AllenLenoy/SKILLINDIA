<?php
require_once 'config/config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS temp_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'user',
        otp VARCHAR(6) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expires_at TIMESTAMP DEFAULT (CURRENT_TIMESTAMP + INTERVAL 10 MINUTE)
    )";

    if ($conn->query($sql)) {
        echo "temp_users table created successfully\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 