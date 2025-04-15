<?php
require_once '../config.php';

header('Content-Type: application/json');

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    try {
        // Get all courses with video count that the user hasn't enrolled in yet
        $stmt = $pdo->prepare("
            SELECT c.*, 
                   (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.course_id) as lessons
            FROM courses c 
            WHERE c.course_id NOT IN (
                SELECT course_id 
                FROM enrollments 
                WHERE user_id = ?
            )
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