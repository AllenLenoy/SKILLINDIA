<?php
require_once '../config.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'] ?? null;
$user_id = $data['user_id'] ?? null;

if (!$course_id || !$user_id) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing parameters'
    ]);
    exit;
}

try {
    // Only update test_given status
    $stmt = $pdo->prepare("
        UPDATE enrollments 
        SET test_given = 1,
            completion_date = CURRENT_TIMESTAMP
        WHERE course_id = ? AND user_id = ?
    ");
    
    $result = $stmt->execute([$course_id, $user_id]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'Failed to update enrollment'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}