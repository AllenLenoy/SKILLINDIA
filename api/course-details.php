<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['course_id']) || !isset($_GET['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$course_id = $_GET['course_id'];
$user_id = $_GET['user_id'];

try {
    // Get course details with enrollment info
    $stmt = $pdo->prepare("
        SELECT c.*, 
               e.total_video_count,
               e.completed_video_count,
               ROUND((e.completed_video_count / e.total_video_count) * 100) as progress
        FROM courses c
        JOIN enrollments e ON c.course_id = e.course_id
        WHERE c.course_id = ? AND e.user_id = ?
    ");
    $stmt->execute([$course_id, $user_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($course) {
        echo json_encode([
            'success' => true,
            'course' => $course
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Course not found or user not enrolled'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>