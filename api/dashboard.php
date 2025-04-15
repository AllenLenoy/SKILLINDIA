<?php
require_once '../config.php';

header('Content-Type: application/json');

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$role = isset($_GET['role']) ? $_GET['role'] : '';

try {
    $response = [
        'success' => true,
        'stats' => [
            'courses' => 0,
            'students' => 0
        ],
        'courses' => []
    ];

    // Simplified query without instructor_id
    $stmt = $pdo->query("
        SELECT 
            c.*, 
            COALESCE(COUNT(DISTINCT e.user_id), 0) as students
        FROM courses c
        LEFT JOIN enrollments e ON c.course_id = e.course_id
        GROUP BY c.course_id
    ");
    
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Update stats
    $response['stats']['courses'] = count($courses);
    $response['stats']['students'] = array_sum(array_column($courses, 'students'));
    
    // Format courses data
    foreach ($courses as &$course) {
        $course['students'] = (int)$course['students'];
    }
    
    $response['courses'] = $courses;
    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load dashboard data: ' . $e->getMessage()
    ]);
}