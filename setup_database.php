<?php
require_once 'config.php';

// Read SQL file
$sql = file_get_contents('database.sql');

// Execute SQL queries
if ($conn->multi_query($sql)) {
    do {
        // Store first result set
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    
    echo "Database tables created successfully!";
} else {
    echo "Error creating database tables: " . $conn->error;
}

$conn->close();
?> 