<?php
require_once '../../config.php';
require_once '../../auth.php';

// Set JSON response headers
header('Content-Type: application/json');

// Ensure user is logged in as admin
if (!isAdmin()) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Fetch all courses with category names
            $stmt = $pdo->prepare("
                SELECT c.*, cat.name as category_name, u.name as instructor_name,
                       (SELECT COUNT(*) FROM course_videos WHERE course_id = c.course_id) as video_count
                FROM courses c
                LEFT JOIN categories cat ON c.category_id = cat.category_id
                LEFT JOIN users u ON c.instructor = u.user_id
                ORDER BY c.created_at DESC
            ");
            $stmt->execute();
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'courses' => $courses
            ]);
            break;

        case 'DELETE':
            // Get request body
            $data = json_decode(file_get_contents('php://input'), true);
            $course_id = (int)$data['course_id'];
            
            // Start transaction
            $pdo->beginTransaction();
            
            // Delete course videos first (due to foreign key constraint)
            $stmt = $pdo->prepare("DELETE FROM course_videos WHERE course_id = ?");
            $stmt->execute([$course_id]);
            
            // Delete course
            $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = ?");
            $stmt->execute([$course_id]);
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Course deleted successfully'
            ]);
            break;

        default:
            throw new Exception('Unsupported request method');
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error in courses API: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while processing your request'
    ]);
} 