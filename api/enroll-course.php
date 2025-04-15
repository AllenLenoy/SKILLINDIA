<?php
require_once '../config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_id']) || !isset($data['course_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

try {
    // Check if already enrolled
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE user_id = ? AND course_id = ?");
    $stmt->execute([$data['user_id'], $data['course_id']]);
    if ($stmt->fetchColumn() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'You are already enrolled in this course'
        ]);
        exit;
    }

    // Get total video count for the course
    $stmt = $pdo->prepare("SELECT video_count FROM courses WHERE course_id = ?");
    $stmt->execute([$data['course_id']]);
    $total_videos = $stmt->fetchColumn();

    // Create enrollment
    $stmt = $pdo->prepare("
        INSERT INTO enrollments (user_id, course_id, status, total_video_count, completed_video_count, enrollment_date, created_at)
        VALUES (?, ?, 'active', ?, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
    ");
    
    $stmt->execute([
        $data['user_id'],
        $data['course_id'],
        $total_videos
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Successfully enrolled in the course'
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>