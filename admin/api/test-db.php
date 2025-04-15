<?php
require_once(__DIR__ . '/../../config.php');

header('Content-Type: text/plain');

try {
    // Test database connection
    echo "Testing database connection...\n";
    $pdo->query("SELECT 1");
    echo "Database connection successful!\n\n";

    // Get all tables from the database
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "Table: $table\n";
        try {
            // Show table structure
            $columns = $pdo->query("SHOW CREATE TABLE `$table`");
            if ($columns) {
                $row = $columns->fetch(PDO::FETCH_ASSOC);
                echo $row['Create Table'] . "\n";
            }
        } catch (PDOException $e) {
            echo "Error reading table structure: " . $e->getMessage() . "\n";
        }
        echo "\n----------------------------------------\n\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 