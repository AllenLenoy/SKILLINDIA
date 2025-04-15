<?php
require_once '../../config.php';

// Set response header
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Log the received data for debugging
error_log("Received POST data: " . print_r($data, true));

// Check if user data is provided
if (!isset($data['user_id']) || !isset($data['role'])) {
    echo json_encode([
        'success' => false,
        'message' => 'User data not provided'
    ]);
    exit();
}

// Check if user is admin
if ($data['role'] !== 'admin') {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit();
}

try {
    // Test database connection
    $pdo->query("SELECT 1");
    
    // Get total users count
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    if (!$stmt) {
        throw new PDOException("Failed to execute users count query");
    }
    $totalUsers = $stmt->fetch()['total'];

    // Get total courses count
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM courses");
    if (!$stmt) {
        throw new PDOException("Failed to execute courses count query");
    }
    $totalCourses = $stmt->fetch()['total'];

    // Get total enrollments count
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM enrollments");
    if (!$stmt) {
        throw new PDOException("Failed to execute enrollments count query");
    }
    $totalEnrollments = $stmt->fetch()['total'];

    // Get recent users
    $stmt = $pdo->query("SELECT user_id, name, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
    if (!$stmt) {
        throw new PDOException("Failed to execute recent users query");
    }
    $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);



    // Return success response with data
    echo json_encode([
        'success' => true,
        'stats' => [
            'users' => $totalUsers,
            'courses' => $totalCourses,
            'enrollments' => $totalEnrollments
        ],
        'recentUsers' => $recentUsers
    ]);

} catch (PDOException $e) {
    error_log("Database error in admin-dashboard.php: " . $e->getMessage());
    error_log("Error code: " . $e->getCode());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred: ' . $e->getMessage()
    ]);
} 