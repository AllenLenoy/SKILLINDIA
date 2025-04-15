<?php
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    // Only fetch published courses
    $stmt = $pdo->prepare("
        SELECT 
            course_id,
            title,
            description,
            video_count,
            playlist_link,
            duration,
            level,
            status,
            created_at
        FROM courses
        WHERE status = 'published'
        ORDER BY created_at DESC
    ");
    
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'courses' => $courses
    ]);
} catch (PDOException $e) {
    error_log("Error fetching courses: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to load courses'
    ]);
} 