<?php
require_once '../config.php';

header('Content-Type: application/json');

// Get user ID from query parameter
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if (!$userId) {
    echo json_encode([
        'success' => false,
        'message' => 'User ID is required'
    ]);
    exit;
}

try {
    // Fetch enrolled courses for the user
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            e.status as enrollment_status,
            e.enrollment_date,
            e.completion_date,
            COALESCE(COUNT(DISTINCT other_e.user_id), 0) as total_students
        FROM enrollments e
        JOIN courses c ON e.course_id = c.course_id
        LEFT JOIN enrollments other_e ON c.course_id = other_e.course_id
        WHERE e.user_id = ?
        GROUP BY c.course_id, e.enrollment_id
        ORDER BY e.enrollment_date DESC
    ");
    
    $stmt->execute([$userId]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the response
    $response = [
        'success' => true,
        'stats' => [
            'total_enrolled' => count($courses),
            'completed' => count(array_filter($courses, fn($c) => $c['enrollment_status'] === 'completed')),
            'in_progress' => count(array_filter($courses, fn($c) => $c['enrollment_status'] === 'active'))
        ],
        'courses' => $courses
    ];
    
    echo json_encode($response);

} catch (PDOException $e) {
    error_log("Error fetching user courses: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load enrolled courses'
    ]);
}