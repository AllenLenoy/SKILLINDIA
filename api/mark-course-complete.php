<?php
require_once '../config/config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'] ?? null;
$user_id = $data['user_id'] ?? null;

if (!$course_id || !$user_id) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Get total video count
    $stmt = $pdo->prepare("SELECT video_count FROM courses WHERE course_id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    // Update enrollment
    $stmt = $pdo->prepare("
        UPDATE enrollments 
        SET completed_video_count = ?, 
            completion_date = CURRENT_TIMESTAMP,
            status = 'completed'
        WHERE course_id = ? AND user_id = ?
    ");
    $stmt->execute([$course['video_count'], $course_id, $user_id]);

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}