<?php
require_once '../config.php';

header('Content-Type: application/json');

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    try {
        // Get total available courses - removed is_active condition
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM courses");
        $total_available = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get user's enrolled courses count
        $stmt = $pdo->prepare("SELECT COUNT(*) as enrolled FROM enrollments WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $total_enrolled = $stmt->fetch(PDO::FETCH_ASSOC)['enrolled'];

        echo json_encode([
            'success' => true,
            'stats' => [
                'total_available' => intval($total_available),
                'total_enrolled' => intval($total_enrolled)
            ]
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