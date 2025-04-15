<?php
require_once '../config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_id']) || !isset($data['course_id']) || !isset($data['video_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Insert into completed_videos if not already completed
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO completed_videos (user_id, course_id, lesson_id, completed_at)
        VALUES (?, ?, ?, CURRENT_TIMESTAMP)
    ");
    $stmt->execute([$data['user_id'], $data['course_id'], $data['video_id']]);

    // Update completed_video_count in enrollments
    $stmt = $pdo->prepare("
        UPDATE enrollments 
        SET completed_video_count = (
            SELECT COUNT(*) 
            FROM completed_videos 
            WHERE user_id = ? AND course_id = ?
        )
        WHERE user_id = ? AND course_id = ?
    ");
    $stmt->execute([
        $data['user_id'], 
        $data['course_id'], 
        $data['user_id'], 
        $data['course_id']
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Video marked as complete'
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>