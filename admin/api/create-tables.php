<?php
require_once(__DIR__ . '/../../config.php');

try {
    // Courses table (if not exists)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `courses` (
        `course_id` int(11) NOT NULL AUTO_INCREMENT,
        `category_id` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `description` text,
        `thumbnail` varchar(255),
        `duration` varchar(50),
        `level` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
        `status` enum('draft','published','archived') DEFAULT 'draft',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`course_id`),
        FOREIGN KEY (`category_id`) REFERENCES `categories`(`category_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "Courses table checked/created successfully!\n";

    // Lessons table (if not exists)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `lessons` (
        `lesson_id` int(11) NOT NULL AUTO_INCREMENT,
        `course_id` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `content` text,
        `video_url` varchar(255),
        `duration` int(11) DEFAULT 0,
        `order_number` int(11) DEFAULT 0,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`lesson_id`),
        FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "Lessons table checked/created successfully!\n";

    // Course Materials table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `course_materials` (
        `material_id` int(11) NOT NULL AUTO_INCREMENT,
        `course_id` int(11) NOT NULL,
        `title` varchar(255) NOT NULL,
        `file_path` varchar(255) NOT NULL,
        `file_type` varchar(50) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`material_id`),
        FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "Course Materials table checked/created successfully!\n";

    // Enrollments table (if not exists)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `enrollments` (
        `enrollment_id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `course_id` int(11) NOT NULL,
        `status` enum('active','completed','dropped') DEFAULT 'active',
        `enrollment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `completion_date` timestamp NULL DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`enrollment_id`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
        FOREIGN KEY (`course_id`) REFERENCES `courses`(`course_id`) ON DELETE CASCADE,
        UNIQUE KEY `user_course_unique` (`user_id`, `course_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "Enrollments table checked/created successfully!\n";

    // Progress Tracking table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `progress_tracking` (
        `progress_id` int(11) NOT NULL AUTO_INCREMENT,
        `enrollment_id` int(11) NOT NULL,
        `lesson_id` int(11) NOT NULL,
        `status` enum('not_started','in_progress','completed') DEFAULT 'not_started',
        `progress_percentage` decimal(5,2) DEFAULT 0.00,
        `last_accessed` timestamp NULL DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`progress_id`),
        FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments`(`enrollment_id`) ON DELETE CASCADE,
        FOREIGN KEY (`lesson_id`) REFERENCES `lessons`(`lesson_id`) ON DELETE CASCADE,
        UNIQUE KEY `enrollment_lesson_unique` (`enrollment_id`, `lesson_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "Progress Tracking table checked/created successfully!\n";

    // Certificates table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `certificates` (
        `certificate_id` int(11) NOT NULL AUTO_INCREMENT,
        `enrollment_id` int(11) NOT NULL,
        `certificate_number` varchar(50) NOT NULL,
        `issue_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `expiry_date` timestamp NULL DEFAULT NULL,
        `status` enum('active','expired','revoked') DEFAULT 'active',
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`certificate_id`),
        FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments`(`enrollment_id`) ON DELETE CASCADE,
        UNIQUE KEY `certificate_number_unique` (`certificate_number`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "Certificates table checked/created successfully!\n";

    // Feedback table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `feedback` (
        `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
        `enrollment_id` int(11) NOT NULL,
        `rating` int(1) NOT NULL CHECK (rating BETWEEN 1 AND 5),
        `comment` text DEFAULT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`feedback_id`),
        FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments`(`enrollment_id`) ON DELETE CASCADE,
        UNIQUE KEY `enrollment_feedback_unique` (`enrollment_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    echo "Feedback table checked/created successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 