<?php
require_once '../config/config.php';

header('Content-Type: application/json');

$course_id = $_GET['course_id'] ?? null;
$user_id = $_GET['user_id'] ?? null;

if (!$course_id || !$user_id) {
    echo json_encode([
        'success' => false,
        'error' => 'Missing parameters'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT test_given, completion_date 
        FROM enrollments 
        WHERE course_id = ? AND user_id = ?
    ");
    $stmt->execute([$course_id, $user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if enrollment exists
    if (!$result) {
        echo json_encode([
            'success' => true,
            'test_given' => false,
            'course_completed' => false,
            'message' => 'No enrollment found'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'test_given' => (bool)$result['test_given'],
        'course_completed' => !is_null($result['completion_date'])
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}