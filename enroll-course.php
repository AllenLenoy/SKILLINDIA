<?php
require_once 'config.php';
require_once 'course_functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = sanitizeInput($_POST['course_id']);
    $user_id = $_SESSION['user_id'];
    $password = sanitizeInput($_POST['password']);
    
    // Verify password
    $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        // Generate and send OTP
        if (enrollUserInCourse($user_id, $course_id)) {
            $_SESSION['success'] = "OTP sent to your email for verification.";
            redirect('otp-verification.php');
        } else {
            $_SESSION['error'] = "Failed to initiate enrollment. Please try again.";
            redirect('dashboard.php');
        }
    } else {
        $_SESSION['error'] = "Invalid password. Please try again.";
        redirect('dashboard.php');
    }
} else {
    redirect('dashboard.php');
}
?>