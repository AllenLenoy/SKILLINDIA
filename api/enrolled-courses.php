<?php
require_once '../config.php';

header('Content-Type: application/json');

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    try {
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.course_id) as lessons,
                   e.total_video_count,
                   e.completed_video_count,
                   ROUND((e.completed_video_count / e.total_video_count) * 100) as progress
            FROM courses c 
            JOIN enrollments e ON c.course_id = e.course_id 
            WHERE e.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'courses' => $courses
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'User ID not provided'
    ]);
}
?>